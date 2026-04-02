<?php

namespace App\Http\Controllers\Api;

use App\Models\ImageLink;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ConvertController extends Controller
{
    public function index(Request $request): View
    {
        $query = ImageLink::latest()->take(6);
        $isAdmin = auth()->check() && auth()->user()->isAdmin();

        if (! $isAdmin) {
            $query = $this->scopeLinksForCurrentVisitor($request, $query);
        }

        $recentLinks = $query->get();
        $isGuest = ! auth()->check();
        $maxUploadMb = $isGuest ? 5 : 100;
        $guestRemainingUploads = $isGuest ? max(0, 5 - $this->guestDailyUploadCount($request)) : null;

        return view('index', compact('recentLinks', 'isGuest', 'maxUploadMb', 'guestRemainingUploads'));
    }

    public function store(Request $request): JsonResponse
    {
        $maxSizeKb = auth()->check() ? 102400 : 5120;

        $request->validate([
            'image'        => "required|image|mimes:jpeg,jpg,png,gif,webp|max:{$maxSizeKb}",
            'custom_alias' => 'nullable|string|min:3|max:30|alpha_dash|unique:image_links,custom_alias|unique:image_links,short_code',
        ], [
            'image.required'          => 'Pilih gambar dulu ya!',
            'image.image'             => 'File harus berupa gambar.',
            'image.mimes'             => 'Format yang didukung: JPG, PNG, GIF, WebP.',
            'image.max'               => 'Ukuran gambar melebihi batas akun kamu.',
            'custom_alias.unique'     => 'Alias ini sudah dipakai, coba yang lain.',
            'custom_alias.min'        => 'Alias minimal 3 karakter.',
            'custom_alias.max'        => 'Alias maksimal 30 karakter.',
            'custom_alias.alpha_dash' => 'Alias hanya boleh huruf, angka, dan tanda hubung.',
        ]);

        if (! auth()->check() && $this->guestDailyUploadCount($request) >= 5) {
            return response()->json([
                'message' => 'Akun guest dibatasi 5 upload per hari. Login untuk batas lebih besar.',
            ], 422);
        }

        $file           = $request->file('image');
        $shortCode      = $this->generateUniqueCode();
        $extension      = $file->getClientOriginalExtension();
        $storedFilename = $shortCode . '_' . time() . '.' . $extension;
        $guestToken     = auth()->check() ? null : $this->resolveGuestToken($request);

        Storage::disk('public')->putFileAs('images', $file, $storedFilename);

        $imageLink = ImageLink::create([
            'user_id'           => auth()->id(),
            'guest_token'       => $guestToken,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename'   => $storedFilename,
            'short_code'        => $shortCode,
            'custom_alias'      => $request->custom_alias ?: null,
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
        ]);

        return response()->json([
            'success'   => true,
            'short_url' => $imageLink->getShortUrl(),
            'image_url' => $imageLink->getImageUrl(),
            'filename'  => $imageLink->original_filename,
            'size'      => $imageLink->getFileSizeFormatted(),
        ]);
    }

    // Redirect publik — siapa saja bisa akses link-nya
    public function redirect(string $code)
    {
        $imageLink = ImageLink::where('short_code', $code)
            ->orWhere('custom_alias', $code)
            ->firstOrFail();

        $imageLink->increment('visit_count');

        return redirect($imageLink->getImageUrl());
    }

    public function list(Request $request): View
    {
        $query = ImageLink::latest();
        $isAdmin = auth()->check() && auth()->user()->isAdmin();

        if (! $isAdmin) {
            $query = $this->scopeLinksForCurrentVisitor($request, $query);
        }

        $links = $query->paginate(12);
        $isGuest = ! auth()->check();

        return view('list', compact('links', 'isGuest'));
    }

    public function destroy(Request $request, ImageLink $imageLink): JsonResponse
    {
        if (! $this->canDelete($request, $imageLink)) {
            return response()->json(['message' => 'Tidak punya akses!'], 403);
        }

        Storage::disk('public')->delete('images/' . $imageLink->stored_filename);
        $imageLink->delete();

        return response()->json(['success' => true]);
    }

    private function generateUniqueCode(int $length = 7): string
    {
        do {
            $code = Str::random($length);
        } while (ImageLink::where('short_code', $code)->exists());

        return $code;
    }

    private function scopeLinksForCurrentVisitor(Request $request, $query)
    {
        if (auth()->check()) {
            return $query->where('user_id', auth()->id());
        }

        return $query->where('guest_token', $this->resolveGuestToken($request));
    }

    private function resolveGuestToken(Request $request): string
    {
        $token = $request->session()->get('guest_upload_token');

        if (! $token) {
            $token = (string) Str::uuid();
            $request->session()->put('guest_upload_token', $token);
        }

        return $token;
    }

    private function guestDailyUploadCount(Request $request): int
    {
        return ImageLink::where('guest_token', $this->resolveGuestToken($request))
            ->whereDate('created_at', now()->toDateString())
            ->count();
    }

    private function canDelete(Request $request, ImageLink $imageLink): bool
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return true;
        }

        if (auth()->check()) {
            return $imageLink->user_id === auth()->id();
        }

        return $imageLink->guest_token !== null
            && $imageLink->guest_token === $this->resolveGuestToken($request);
    }
}
