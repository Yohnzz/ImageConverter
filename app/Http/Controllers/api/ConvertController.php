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
    // 1. Cek Limit Upload Harian untuk Guest SEBELUM validasi file
    if (!auth()->check()) {
        $guestUploadCount = $this->guestDailyUploadCount($request);
        if ($guestUploadCount >= 5) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Limit tercapai! Kamu sudah mencapai batas upload gratis hari ini (5/hari). Silakan login atau coba lagi besok.',
            ], 429); // 429 = Too Many Requests
        }
    }

    // 2. Tentukan limit ukuran berdasarkan status login
    $maxSizeKb = auth()->check() ? 102400 : 5120; // 100MB vs 5MB
    $maxSizeMb = auth()->check() ? 100 : 5;

    // 3. Validasi Input
    $request->validate([
        'image'        => "required|image|mimes:jpeg,jpg,png,gif,webp|max:{$maxSizeKb}",
        'custom_alias' => 'nullable|string|min:3|max:30|alpha_dash|unique:image_links,custom_alias|unique:image_links,short_code',
    ], [
        'image.required'          => 'Pilih gambar dulu ya!',
        'image.image'             => 'File harus berupa gambar (JPG, PNG, GIF, WebP).',
        'image.mimes'             => 'Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.',
        'image.max'               => auth()->check() 
            ? "❌ File terlalu besar! Maksimal upload adalah 100 MB per file."
            : "❌ File terlalu besar! Guest hanya bisa upload maksimal 5 MB per file. Silakan login untuk upload hingga 100 MB.",
        'custom_alias.unique'     => '❌ Alias ini sudah dipakai, coba yang lain.',
        'custom_alias.min'        => 'Alias minimal 3 karakter.',
        'custom_alias.max'        => 'Alias maksimal 30 karakter.',
        'custom_alias.alpha_dash' => 'Alias hanya boleh huruf, angka, dan (-) underscore.',
    ]);

    try {
        $file = $request->file('image');
        
        // Cek ulang ukuran file (double check)
        if ($file->getSize() > ($maxSizeKb * 1024)) {
            return response()->json([
                'success' => false,
                'message' => auth()->check() 
                    ? "❌ File terlalu besar! Maksimal upload adalah 100 MB per file. File Anda: " . round($file->getSize() / 1048576, 2) . " MB"
                    : "❌ File terlalu besar! Guest hanya bisa upload maksimal 5 MB per file. File Anda: " . round($file->getSize() / 1024, 2) . " KB",
            ], 413);
        }

        $shortCode = $this->generateUniqueCode();
        $extension = strtolower($file->getClientOriginalExtension());
        $storedFilename = $shortCode . '_' . time() . '.' . $extension;

        // Simpan file ke storage/app/public/images
        $file->storeAs('images', $storedFilename, 'public');

        // Simpan ke Database
        $imageLink = ImageLink::create([
            'user_id'           => auth()->check() ? auth()->id() : null,
            'guest_token'       => auth()->check() ? null : $this->resolveGuestToken($request),
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
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => $e->errors()['image'][0] ?? 'Validasi gambar gagal.',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan gambar. Pastikan folder storage dapat ditulis atau coba lagi nanti.',
            'debug'   => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

private function resolveGuestToken(Request $request): string
{
    // Jika user login, kita tidak butuh guest_token
    if (auth()->check()) return ''; 

    $token = $request->session()->get('guest_upload_token');

    if (!$token) {
        // Gunakan kombinasi Session ID dan string unik agar lebih stabil
        $token = (string) Str::uuid();
        $request->session()->put('guest_upload_token', $token);
    }

    return $token;
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
