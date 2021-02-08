<?php

namespace App\Models;

use App\Filter;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'order', 'question',
    ];

    /**
     * Get item's question.
     *
     * @return Model
     */
    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'question', 'id')->first();
    }

    /**
     * Get images on an item.
     *
     * @param User $user
     * @return Model
     */
    public function images()
    {
        return $this->hasMany('App\Models\Image', 'model_id', 'id')->where(['model' => 'Item']);
    }

    /**
     * Get the item's order as A, B, C, D, etc.
     *
     * @return string
     */
    public function orderStr(){
        return chr(65 + $this->order);
    }

    /**
     * Get the html code for item.
     * 
     * @return string
     */
    public function render($filter = []) {
        $f = new Filter($this->content, [
            'addLaTeX',
            'scapeHtmlSpecialChars',
            'addLinks',
            'addImages',
            'addRichFormat'
        ]);

        return $f->render();
    }

}
