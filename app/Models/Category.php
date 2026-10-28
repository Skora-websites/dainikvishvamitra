<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $table = 'categories';

    protected $fillable = ['name_hi', 'slug', 'description', 'accent_color', 'pills', 'sort_order', 'show_in_nav', 'show_in_footer', 'status'];

    protected $casts = [
        'show_in_nav' => 'boolean',
        'show_in_footer' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }
}
