<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityProgress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'activity'
    ];
    
    public $timestamps = false;
}
