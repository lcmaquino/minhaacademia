<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\User' => 'App\Policies\UserPolicy',
        'App\Course' => 'App\Policies\CoursePolicy',
        'App\Module' => 'App\Policies\ModulePolicy',
        'App\Lesson' => 'App\Policies\LessonPolicy',
        'App\Activity' => 'App\Policies\ActivityPolicy',
        'App\Question' => 'App\Policies\QuestionPolicy',
        'App\Item' => 'App\Policies\ItemPolicy',
        'App\Certify' => 'App\Policies\CertifyPolicy',
        'App\Image' => 'App\Policies\ImagePolicy',
        'App\Setting' =>  'App\Policies\SettingPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
