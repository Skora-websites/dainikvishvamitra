<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionEvent extends Model
{
    public $timestamps = false;

    protected $table = 'election_events';

    protected $fillable = ['state_name', 'date_text', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
