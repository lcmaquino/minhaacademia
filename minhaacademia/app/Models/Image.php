<?php

namespace App\Models;

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
        $nameSpace = 'App\\Models\\';
        return $this->belongsTo($nameSpace . $model, 'model_id', 'id')->first();
    }
}
