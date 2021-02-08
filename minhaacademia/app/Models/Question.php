<?php

namespace App\Models;

use App\Filter;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'order', 'activity', 'answer',
    ];

    /**
     * Get the activity that the question belongs to.
     *
     * @return Model
     */
    public function activity()
    {
        return $this->belongsTo('App\Models\Activity', 'activity', 'id')->first();
    }

    /**
     * Get the items in a question.
     *
     * @return Model
     */
    public function items()
    {
        return $this->hasMany('App\Models\Item', 'question', 'id')->orderBy('order', 'asc');
    }

    /**
     * Get the images in a question.
     *
     * @return Model
     */
    public function images()
    {
        return $this->hasMany('App\Models\Image', 'model_id', 'id')->where(['model' => 'Question']);
    }

    /**
     * Get the number of items in a question.
     *
     * @return integer
     */
    public function itemsCount(){
        return $this->items()->count();
    }

    /**
     * Get the question answer like A, B, C, D, etc.
     *
     * @return integer
     */
    public function answerStr(){
        return chr(65 + $this->answer);
    }

    /**
     * Get the answer item.
     *
     * @return Model
     */
    public function answerItem(){
        return Item::where(['question' => $this->id, 'order' => $this->answer])->first();
    }

    /**
     * Get the html code for question.
     *
     * @return string
     */
    public function render() {
        $f = new Filter($this->content);
        return $f->render();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($question) {
            $course = $question->activity()->module()->course();
            $course->updated_at = now();
            $course->save();
        });

        static::updated(function ($question) {
            if($question->wasChanged('answer')) {
                ActivityProgress::where(['activity' => $question->activity])->delete();
                $course = $question->activity()->module()->course();
                $course->updated_at = now();
                $course->save();
            }
        });

        static::deleted(function ($question) {
            $course = $question->activity()->module()->course();
            $course->updated_at = now();
            $course->save();
        });
    }
}
