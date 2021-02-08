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
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\Course' => 'App\Policies\CoursePolicy',
        'App\Models\Module' => 'App\Policies\ModulePolicy',
        'App\Models\Lesson' => 'App\Policies\LessonPolicy',
        'App\Models\Activity' => 'App\Policies\ActivityPolicy',
        'App\Models\Question' => 'App\Policies\QuestionPolicy',
        'App\Models\Item' => 'App\Policies\ItemPolicy',
        'App\Models\Certify' => 'App\Policies\CertifyPolicy',
        'App\Models\Image' => 'App\Policies\ImagePolicy',
        'App\Models\Setting' =>  'App\Policies\SettingPolicy',
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
