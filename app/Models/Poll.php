<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    public $timestamps = false;

    protected $table = 'polls';

    protected $fillable = ['question', 'context', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function options()
    {
        return $this->hasMany(PollOption::class, 'poll_id')->orderBy('sort_order');
    }
}
