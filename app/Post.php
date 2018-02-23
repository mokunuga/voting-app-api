<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $fillable = [
        'name',
    ];

    public function candidates()
    {
        return $this->hasMany('App\Candidate', 'post_id');
    }
}
