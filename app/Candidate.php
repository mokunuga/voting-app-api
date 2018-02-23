<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    //
    protected $fillable = [
        'first_name', 'last_name', 'post_id', 'manifesto', 'candidate_image',
    ];

    public function post()
    {
        return $this->belongsTo('App\Post', 'post_id');
    }
}
