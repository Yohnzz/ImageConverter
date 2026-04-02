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
    // Jika admin, ambil 6 gambar terbaru dari SIAPA PUN
    // Jika user, ambil 6 gambar terbaru MILIK SENDIRI
    $query = ImageLink::latest()->take(6);

    if (auth()->user()->role !== 'admin') {
        $query->where('user_id', auth()->id());
    }

    $recentLinks = $query->get();

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
    // Logika yang sama untuk halaman "Semua Link" (dengan pagination)
    $query = ImageLink::latest();

    if (auth()->user()->role !== 'admin') {
        $query->where('user_id', auth()->id());
    }

    $links = $query->paginate(12);

    return view('list', compact('links'));
}

    public function destroy(ImageLink $imageLink)
{
    // Izinkan jika dia Admin ATAU dia pemilik gambarnya
    if (auth()->user()->role === 'admin' || $imageLink->user_id === auth()->id()) {
        Storage::delete('public/images/' . $imageLink->stored_filename);
        $imageLink->delete();
        return response()->json(['success' => true]);
    }

    return response()->json(['message' => 'Tidak punya akses!'], 403);
}

    private function generateUniqueCode(int $length = 7): string
    {
        do {
            $code = Str::random($length);
        } while (ImageLink::where('short_code', $code)->exists());

        return $code;
    }
}