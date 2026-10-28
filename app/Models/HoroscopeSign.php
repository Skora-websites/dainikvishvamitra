<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoroscopeSign extends Model
{
    public $timestamps = false;

    protected $table = 'horoscope_signs';

    protected $fillable = ['name_hi', 'name_en', 'symbol', 'prediction', 'lucky_color', 'lucky_number', 'deity', 'sort_order'];
}
