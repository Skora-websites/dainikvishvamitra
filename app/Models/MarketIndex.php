<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketIndex extends Model
{
    public $timestamps = false;

    protected $table = 'market_indices';

    protected $fillable = ['label', 'value_text', 'change_text', 'direction', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
