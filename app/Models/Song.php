<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    // Tambahkan ini agar kolom-kolom ini bisa diisi secara massal
    protected $fillable = [
        'youtube_id',
        'title',
        'artist',
        'cover',
        'duration'
    ];
}