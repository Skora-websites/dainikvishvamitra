<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public $timestamps = false;

    protected $table = 'pages';

    protected $fillable = ['title', 'slug', 'body', 'status'];

    protected $casts = [
        'updated_at' => 'datetime',
    ];
}
