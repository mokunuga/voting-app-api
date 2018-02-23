<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    //
    protected $fillable = [
        'candidate_id', 'vote_count', 'user_id',
    ];
}
