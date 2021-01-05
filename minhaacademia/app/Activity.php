<?php

namespace App;

use App\Filter;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'module', 'order',
    ];

    public $timestamps = false;

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
     * Get the questions in an activity.
     *
     * @return Model
     */
    public function questions()
    {
        return $this->hasMany('App\Question', 'activity', 'id')->orderBy('order', 'asc');
    }

    /**
     * Get the images in an activity.
     *
     * @return Model
     */
    public function images()
    {
        return $this->hasMany('App\Image', 'model_id', 'id')->where(['model' => 'Activity']);
    }

    /**
     * Get the number of questions in an activity.
     *
     * @return integer
     */
    public function questionscount()
    {
        return $this->questions->count();
    }

    /**
     * Get activity short title.
     *
     * @return string
     */
    public function shortTitle() {
        $f = new Filter();
        return $f->short($this->title, 32);
    }
    
    /**
     * Verify if activity is completed.
     *
     * @param User $user
     * @return boolean
     */
    public function isCompleted(User $user){
        $activity_progress = ActivityProgress::where(['user' => $user->id, 'activity' => $this->id])->get();
        return ($activity_progress->count() > 0);
    }

    /**
     * Store the user's progress.
     * 
     * @param User $user
     * @return Model
     */
    public function storeProgress(User $user){
        $activity_progress = ActivityProgress::create(['user' => $user->id, 'activity' => $this->id]);
        return $activity_progress;
    }

    /**
     * Get the html code for activity page.
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
        static::created(function ($activity) {
            $course = $activity->module()->course();
            $course->updated_at = now();
            $course->save();
        });

        static::deleted(function ($activity) {
            $course = $activity->module()->course();
            $course->updated_at = now();
            $course->save();
        });
    }
}
