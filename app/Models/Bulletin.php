<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    public $timestamps = false;

    protected $table = 'bulletins';

    protected $fillable = ['placement', 'source', 'text', 'time_text', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
