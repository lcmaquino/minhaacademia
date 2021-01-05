<?php

namespace App\Http\Middleware;

use Closure;
use App\Course;
use App\Module;
use App\Certify;
use Illuminate\Support\Facades\Auth;

class CheckProgress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $course = $request->route('course');
        $lesson = null;

        if (empty($course)) {
            $activity = $request->route('activity');
            if (!empty($activity)) {
                $course = $activity->module()->course();
            }else{
                $lesson = $request->route('lesson');
                if (!empty($lesson)) {
                    $course = $lesson->module()->course();
                }
            }
        }

        if (!empty($user)) {
            if (!empty($lesson) && !$lesson->isCompleted($user)) {
                $lesson->storeProgress($user);
            }
            
            if (!empty($course) && !empty($user->cpf) && !empty($user->name) 
                && $course->calculateProgress($user) == 100) {
                $certify = $course->getUserCertify($user);

                if (empty($certify)){
                    $certify = new Certify();
                    $certify->name = $user->name;
                    $certify->cpf = $user->cpf;
                    $certify->title = $course->title;
                    $certify->duration = $course->duration;
                    $certify->codeGenerator();
                    $certify->save();
                }
            }

        }

        return $next($request);
    }
}
