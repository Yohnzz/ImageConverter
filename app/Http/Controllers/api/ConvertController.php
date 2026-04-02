<?php

namespace App\Http\Controllers\Api;

use App\Models\ImageLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ConvertController extends Controller
{
    // Hanya user yang login bisa akses
    public function __construct()
    {
        $this->middleware('auth')->except(['redirect']);
    }

    public function index()
    {
        // Hanya ambil gambar milik user yang sedang login
        $recentLinks = ImageLink::where('user_id', auth()->id())
            ->latest()
            ->take(6)
            ->get();

        return view('index', compact('recentLinks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'        => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'custom_alias' => 'nullable|string|min:3|max:30|alpha_dash|unique:image_links,custom_alias|unique:image_links,short_code',
        ], [
            'image.required'          => 'Pilih gambar dulu ya!',
            'image.image'             => 'File harus berupa gambar.',
            'image.mimes'             => 'Format yang didukung: JPG, PNG, GIF, WebP.',
            'image.max'               => 'Ukuran gambar maksimal 10MB.',
            'custom_alias.unique'     => 'Alias ini sudah dipakai, coba yang lain.',
            'custom_alias.min'        => 'Alias minimal 3 karakter.',
            'custom_alias.max'        => 'Alias maksimal 30 karakter.',
            'custom_alias.alpha_dash' => 'Alias hanya boleh huruf, angka, dan tanda hubung.',
        ]);

        $file           = $request->file('image');
        $shortCode      = $this->generateUniqueCode();
        $extension      = $file->getClientOriginalExtension();
        $storedFilename = $shortCode . '_' . time() . '.' . $extension;

        $file->storeAs('public/images', $storedFilename);

        $imageLink = ImageLink::create([
            'user_id'           => auth()->id(), // ← Simpan user_id
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

    public function list()
    {
        // Hanya tampilkan link milik user yang login
        $links = ImageLink::where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('list', compact('links'));
    }

    public function destroy(ImageLink $imageLink)
    {
        // Pastikan hanya pemilik yang bisa hapus
        if ($imageLink->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Storage::delete('public/images/' . $imageLink->stored_filename);
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
}