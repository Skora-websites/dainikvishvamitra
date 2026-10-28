<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Epaper extends Model
{
    public $timestamps = false;

    protected $table = 'epapers';

    protected $fillable = ['title', 'edition_date', 'pdf_path', 'cover_image', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'edition_date' => 'date',
        'created_at' => 'datetime',
    ];
}
