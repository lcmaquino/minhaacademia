<?php

namespace App;
use App\Course;
use App\Module;
use App\Lesson;
use App\Activity;
use App\LessonProgress;
use App\ActivityProgress;
use App\CPFFormatter;

use Illuminate\Database\Eloquent\Model;

class Certify extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'cpf', 'course', 'code',
    ];

    /**
     * Show CPF like "000.000.000-00".
     *
     * @return string
     */
    public function cpfShow()
    {
        $cpf = new CPFFormatter($this->cpf);
        return $cpf->show();
    }

    /**
     * Show CPF like "***-000.000-**".
     *
     * @return string
     */
    public function cpfMask()
    {
        $cpf = new CPFFormatter($this->cpf);
        return $cpf->mask();
    }

    /**
     * Generate a random code.
     *
     * @param integer $len
     * @return string
     */
    public function codeGenerator($len = 8){
        do {
            $code = md5(uniqid(rand(), true));
            $len = $len < strlen($code) ? $len : strlen($code)/2;
            $code = substr($code, rand(0, strlen($code) - $len), $len);
        }while(!empty(Certify::where(['code' => $code])->first()));
        $this->code = $code;
        return $this->code;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleted(function ($certify) {
            $course = Course::where([
                    ['title', 'like', $certify->title],
                    'duration' => $certify->duration,
                ])->first();
            if(!empty($course)) {
                $user = User::where(['cpf' => $certify->cpf])->first();
                foreach ($course->modules as $module) {
                    foreach($module->lessons as $lesson) {
                        LessonProgress::where([
                            'user' => $user->id,
                            'lesson' => $lesson->id,
                            ])->delete();
                    }

                    foreach($module->activities as $activity) {
                        ActivityProgress::where([
                            'user' => $user->id,
                            'activity' => $activity->id,
                            ])->delete();
                    }
                }
            }
        });
    }

}
