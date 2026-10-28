<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyPoint extends Model
{
    public $timestamps = false;

    protected $table = 'key_points';

    protected $fillable = ['article_id', 'point', 'sort_order'];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
