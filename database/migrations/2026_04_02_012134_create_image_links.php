<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_token', 64)->nullable()->index();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('short_code', 10)->unique();
            $table->string('custom_alias')->nullable()->unique();
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type');
            $table->unsignedInteger('visit_count')->default(0);
            $table->timestamps();
            $table->index(['guest_token', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_links');
    }
};
