<?php

namespace App\Models;

use App\Filter;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'icon', 'title', 'video', 'description', 'duration', 'teacher',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'icon' => 'json',
    ];

    /**
     * Get the modules on a course.
     *
     * @return Model
     */
    public function modules()
    {
        return $this->hasMany('App\Models\Module', 'course', 'id')->orderBy('order', 'asc');
    }

    /**
     * Get the images on a course.
     *
     * @return Model
     */
    public function images()
    {
        return $this->hasMany('App\Models\Image', 'model_id', 'id')->where(['model' => 'Course']);
    }
    
    /**
     * Get the user's progress.
     *
     * @param User $user
     * @return integer
     */
    public function calculateProgress(User $user){
        $mod = 0;
        $modCount = 0;

        foreach ($this->modules as $module) {
            $mod += $module->calculateProgress($user);
            $modCount++;
        }

        return $modCount > 0? round($mod/$modCount): 0;
    }

    /**
     * Get user's certify.
     *
     * @param User $user
     * @return Model
     */
    public function getUserCertify(User $user) {
        return Certify::where(['cpf' => $user->cpf])
            ->whereRaw( 'LOWER(`title`) like ? AND `duration` = ?', [
                strtolower($this->title),
                $this->duration,
                ])
            ->first();
    }

    /**
     * Get the number of modules.
     *
     * @return integer
     */
    public function modulesCount(){
        return $this->modules()->count();
    }

    /**
     * Get the number of lessons.
     *
     * @return integer
     */
    public function lessonsCount() {
        $total = 0;
        foreach ($this->modules as $module) {
            foreach ($module->lessons as $lesson) {
                    $total++;
            }
        }
        return $total;
    }

    /**
     * Get the number of activities.
     *
     * @return integer
     */
    public function activitiesCount() {
        $total = 0;
        foreach ($this->modules as $module) {
            foreach ($module->activities as $activity) {
                $total++;
            }
        }
        return $total;
    }

    /**
     * Get the number of completed lessons.
     *
     * @param User $user
     * @return integer
     */
    public function lessonsCompletedCount(User $user) { 
        $total = 0;
        foreach ($this->modules as $module) {
            $total += $module->lessonsCompletedCount($user);
        }
        return $total;
    }

    /**
     * Get the number of completed activities.
     *
     * @param User $user
     * @return integer
     */
    public function activitiesCompletedCount(User $user) {
        $total = 0;
        foreach ($this->modules as $module) {
            $total += $module->activitiesCompletedCount($user);
        }
        return $total;
    }

    /**
     * Get the html code for course page.
     * 
     * @return string
     */
    public function render() {
        $f = new Filter($this->description);
        return $f->render();
    }

    /**
     * Get the course short description.
     *
     * @return string
     */
    public function shortDescription(){
        $f = new Filter();
        return $f->short($this->description, 65);
    }

    /**
     * Get the course short title.
     *
     * @return string
     */
    public function shortTitle(){
        $f = new Filter();
        return $f->short($this->title, 32);
    }
}
