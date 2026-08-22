<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_id')->unique(); // <--- Tambahkan kolom ini
            $table->string('title');                  // <--- Dan kolom ini
            $table->string('artist');                 // <--- Dan ini
            $table->string('cover');                  // <--- Dan ini
            $table->integer('duration')->default(180); // <--- Dan ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};