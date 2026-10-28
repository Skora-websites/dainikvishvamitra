<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    public $timestamps = false;

    protected $table = 'poll_options';

    protected $fillable = ['poll_id', 'option_text', 'votes', 'color_class', 'sort_order'];

    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }
}
