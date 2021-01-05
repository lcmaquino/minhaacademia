<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'model', 'model_id',
    ];

    /**
     * Get image's model.
     *
     * @param string $model
     * @return Model
     */
    public function model($model = '')
    {
        return $this->belongsTo('App\\' . $model, 'model_id', 'id')->first();
    }
}
