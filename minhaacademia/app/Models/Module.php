<?php

namespace App\Models;

use App\Filter;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'course', 'order',
    ];

    /**
     * Get the course that the module belongs to.
     *
     * @return Model
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course', 'id')->first();
    }

    /**
     * Get the lessons on a module.
     *
     * @return Model
     */
    public function lessons()
    {   
        return $this->hasMany('App\Models\Lesson', 'module', 'id')->orderBy('order', 'asc');
    }

    /**
     * Get the activities on a module.
     *
     * @return Model
     */
    public function activities()
    {
        return $this->hasMany('App\Models\Activity', 'module', 'id')->orderBy('order', 'asc');
    }

    /**
     * Get module short title.
     *
     * @return string
     */
    public function shortTitle() {
        $f = new Filter();
        return $f->short($this->title, 64);
    }

    /**
     * Check if an user has completed the lessons in a module.
     *
     * @param User $user
     * @return boolean
     */
    public function isLessonsCompleted(User $user) {
        if ($this->lessons()->count() > 0) {
            foreach($this->lessons as $lesson) {
                if(!$lesson->isCompleted($user)){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check if an user has completed the activities in a module.
     *
     * @param User $user
     * @return boolean
     */
    public function isActivitiesCompleted(User $user) {
        if ($this->activities()->count() > 0) {
            foreach($this->activities as $activity) {
                if(!$activity->isCompleted($user)){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the number of lessons in a module.
     *
     * @return integer
     */
    public function lessonsCount(){
        return $this->lessons()->count();
    }

    /**
     * Get the number of activities in a module.
     *
     * @return integer
     */
    public function activitiesCount(){
        return $this->activities()->count();
    }

    /**
     * Get the number of completed lessons in a module.
     *
     * @param User $user
     * @return integer
     */
    public function lessonsCompletedCount(User $user) {
        $n = 0;
        foreach ($this->lessons as $lesson) {
            if($lesson->isCompleted($user))
                $n++;
        }
        return $n;
    }

    /**
     * Get the number of completed activities in a module.
     *
     * @param User $user
     * @return integer
     */
    public function activitiesCompletedCount(User $user) {
        $n = 0;
        foreach ($this->activities as $activity) {
            if($activity->isCompleted($user))
                $n++;
        }
        return $n;
    }

    /**
     * Get the user's progress.
     *
     * @param User $user
     * @return integer
     */
    public function calculateProgress(User $user){
        $total = $this->lessonsCount() + $this->activitiesCount();
        if ($total == 0)
            return 0;

        $complete = $this->lessonsCompletedCount($user) + $this->activitiesCompletedCount($user);
        return round(100.0*$complete/$total);
    }
}
