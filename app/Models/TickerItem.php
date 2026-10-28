<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TickerItem extends Model
{
    public $timestamps = false;

    protected $table = 'ticker_items';

    protected $fillable = ['slot', 'text', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
