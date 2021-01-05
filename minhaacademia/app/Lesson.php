<?php

namespace App;

use App\Filter;
use App\LessonProgress;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'video', 'description', 'module', 'order',
    ];

    /**
     * Get the module that the activity belongs to.
     *
     * @return Model
     */
    public function module()
    {
        return $this->belongsTo('App\Module', 'module', 'id')->first();
    }

    /**
     * Get the images on a lesson.
     *
     * @return Model
     */
    public function images()
    {
        return $this->hasMany('App\Image', 'model_id', 'id')->where(['model' => 'Lesson']);
    }

    /**
     * Check if user has completed the lesson.
     *
     * @param User $user
     * @return boolean
     */
    public function isCompleted(User $user){
        $lesson_progress = LessonProgress::where(['user' => $user->id, 'lesson' => $this->id])->get();
        return ($lesson_progress->count() > 0);
    }

    /**
     * Store the user's progress.
     *
     * @param User $user
     * @return Model
     */
    public function storeProgress(User $user){
        $lesson_progress = LessonProgress::create(['user' => $user->id, 'lesson' => $this->id]);
        return $lesson_progress;
    }

    /**
     * Get lesson short title.
     *
     * @return string
     */
    public function shortTitle() {
        $f = new Filter();
        return $f->short($this->title, 32);
    }

    /**
     * Get the html code for lesson page.
     *
     * @return string
     */
    public function render() {
        $f = new Filter($this->description);
        return $f->render();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($lesson) {
            $course = $lesson->module()->course();
            $course->updated_at = now();
            $course->save();
        });

        static::updated(function ($lesson) {
            if($lesson->wasChanged('video')) {
                LessonProgress::where(['lesson' => $lesson->id])->delete();
                $course = $lesson->module()->course();
                $course->updated_at = now();
                $course->save();
            }
        });

        static::deleted(function ($lesson) {
            $course = $lesson->module()->course();
            $course->updated_at = now();
            $course->save();
        });
    }
}