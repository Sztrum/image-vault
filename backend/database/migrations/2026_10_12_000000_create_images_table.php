<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('original_name');
            $table->string('extension', 10);
            $table->string('mime_type', 50);
            $table->unsignedInteger('size_bytes');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->string('storage_path');
            $table->string('thumbnail_path');
            $table->string('uploader_name', 100);
            $table->string('uploader_email');
            $table->json('exif')->nullable();
            $table->json('iptc')->nullable();
            $table->decimal('temperature_celsius', 4, 1)->nullable();
            $table->dateTime('temperature_measured_at')->nullable();
            $table->timestamps();

            $table->index(['created_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
