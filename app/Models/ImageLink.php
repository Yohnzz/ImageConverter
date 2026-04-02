<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageLink extends Model
{
    protected $fillable = [
        'user_id',
        'guest_token',
        'original_filename',
        'stored_filename',
        'short_code',
        'custom_alias',
        'file_size',
        'mime_type',
        'visit_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getShortUrl(): string
    {
        $identifier = $this->custom_alias ?? $this->short_code;
        return url("/i/{$identifier}");
    }

    public function getImageUrl(): string
    {
        return url("/storage/images/{$this->stored_filename}");
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
