<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Role;
use App\Models\Certify;
use App\Models\Setting;
use App\Menu;
use App\Filter;
use Illuminate\Support\Facades\Auth;

class MenuGenerator {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {   

        $request = $this->_getNavigationMenu($request, 'ul', 'navmenu', ['navmenu']);
        $request = $this->_getSocialMenu($request, 'ul', 'socialmenu', ['socialmenu']);

        $controllerClass = class_basename(get_class($request->route()->getController()));
        $method = 'get' . $controllerClass . 'Menu';

        if (method_exists(new MenuGenerator(), $method)) {
            $request = call_user_func(MenuGenerator::class . '::'. $method, $request, 'ul', 'sidebarmenu', ['sidebarmenu']);
        }

        $channeltTitleSetting = Setting::where(['key' => 'youtube_channel_title'])->first();
        $channeltTitle = empty($channeltTitleSetting) ? '' : $channeltTitleSetting->value;

        $appNameSetting = Setting::where(['key' => 'app_name'])->first();
        $appName = empty($appNameSetting) ? '' : $appNameSetting->value;

        $appAuthorSetting = Setting::where(['key' => 'app_author'])->first();
        $appAuthor = empty($appAuthorSetting) ? '' : $appAuthorSetting->value;

        $appDescriptionSetting = Setting::where(['key' => 'app_description'])->first();
        $appDescription = empty($appDescriptionSetting) ? '' : $appDescriptionSetting->value;

        if ($request->has('pagetitle')) {
            $pagetitle = $request->get('pagetitle') . ' - ' . $channeltTitle;
        }else{
            $pagetitle = $channeltTitle;
        }

        $request->merge([
            'pagetitle' => $pagetitle, 
            'appName' => $appName,
            'appAuthor' => $appAuthor,
            'appDescription' => $appDescription,
        ]);

        return $next($request);
    }

    /**
     * Get html code for certify link.
     *
     * @param $course
     * @param $user
     * @return string
     */
    protected function _getCertifyLink($course = null, $user = null){
        $certifyLink = '';
        if (!empty($user) && !empty($course)) {
            $certifyLink = '<span class="module-info">(' . $course->calculateProgress($user) . '%)</span>';
            if ($course->calculateProgress($user) == 100) {
                $certify = $course->getUserCertify($user);
                $certifyLink = '<span class="module-info">(' . $course->calculateProgress($user) . '%)</span>';
    
                if (empty($certify)) {
                    $certifyLink .= '<a href="' . route('mypanel') 
                        . '" title="Certificado"><i class="fa fa-file-text-o"></i></a>';
                }else{
                    $certifyLink .= '<a href="' . route('certifyShow', ['certify' => $certify]) 
                        . '" title="Certificado"><i class="fa fa-file-text-o"></i></a>';
                }
            }
        }
        return $certifyLink;
    }

    /**
     * Get html code for sidebar menu.
     *
     * @param $course
     * @param $user
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @param $model
     * @return string
     */
    protected function _getSideBarMenu($course = null, $user = null, $type = '', $menuname = '', $menuclass = [],  $model = null){
        $mt = $type;
        $mc = $menuclass;
        $menu = new Menu($mt, $mc);
        $html = '';
        $modelClassBaseName = empty($model) ? '' : class_basename(get_class($model));

        if (!empty($course)) {
            foreach($course->modules as $module) {
                $html .= '<p title="' . $module->title . '"><span class="module-info">' . ($module->order + 1) . '.</span> ' . $module->shortTitle() . '</p>';
    
                if ($module->lessons->count()) {
                    if (empty($user)) {
                        $html .= '<p><span class="module-info"><small>Aulas</small></span></p>';
                    }else{
                        $html .= '<p><span class="module-info"><small>Aulas (' . $module->lessonsCompletedCount($user) . '/' . $module->lessonsCount() . ')</small></span></p>';
                    }

                    $menu = new Menu($mt, $mc);

                    foreach ($module->lessons as $lesson) {
                        if(empty($user)) {
                            $url = route('lessonShow', ['lesson' => $lesson->id]);
                            $icon = [];
                            $text = $lesson->title;
                            $class = [];
                            $menu->addWithIcon($url, $icon, $lesson->shortTitle(), $class, false, $text);
                        }else{
                            $url = route('lessonShow', ['lesson' => $lesson->id]);
                            $icon = $lesson->isCompleted($user) ? ['fa', 'fa-check-circle-o'] : ['fa', 'fa-circle-o'];
                            $text = $lesson->title;
                            $class = ($modelClassBaseName == 'Lesson') && ($lesson->id == $model->id) ? ['is-active'] : [];

                            //if ($lesson->id == 7) dd($lesson->title, $lesson->shortTitle());

                            $menu->addWithIcon($url, $icon, $lesson->shortTitle(), $class, false, $text);
                        }
                    }
                    $html .= $menu->render();
                }
    
                if ($module->activities->count()) {
                    if (empty($user)) {
                        $html .= '<p><span class="module-info"><small>Atividades</small></span></p>';
                    }else{
                        $html .= '<p><span class="module-info"><small>Atividades (' . $module->activitiesCompletedCount($user) . '/' . $module->activitiesCount() . ')</small></span></p>';
                    }
                    $menu = new Menu($mt, $mc);
                    foreach ($module->activities as $activity) {
                        if(empty($user)) {
                            $url = route('activityShow', ['activity' => $activity->id]);
                            $icon = [];
                            $text = $activity->title;
                            $class = [];
                            $menu->addWithIcon($url, $icon, $activity->shortTitle(), $class, false, $text);
                        }else{
                            $url = route('activityShow', ['activity' => $activity->id]);
                            $icon = $activity->isCompleted($user) ? ['fa', 'fa-check-circle-o'] : ['fa', 'fa-circle-o'];
                            $text = $activity->title;
                            $class = ($modelClassBaseName == 'Activity') && ($activity->id == $model->id) ? ['is-active'] : [];
                            $menu->addWithIcon($url, $icon, $activity->shortTitle(), $class, false, $text);
                        }
                    }
                    $html .= $menu->render();
                }
            }
        }

        return $html;
    }

    /**
     * Get html code for navigation menu.
     *
     * @param $request
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @return Request
     */
    protected function _getNavigationMenu($request, $type = '', $menuname = '', $menuclass = []) {
        $mn = $menuname;
        $mt = $type;
        $mc = $menuclass;
        $menu = new Menu($mt, $mc);

        $faGraduationCap = ['fa', 'fa-graduation-cap', 'dark-gray', 'size-m'];
        $faSearch = ['fa', 'fa-search', 'dark-gray', 'size-m'];
        $faGear = ['fa', 'fa-gear', 'dark-gray', 'size-m'];

        if(Auth::check()) {
            $menu->addWithIcon(
                route('certifyVerify'), 
                $faSearch,
                '',
                [],
                true,
                'Verificar certificado'
            );

            $menu->addWithIcon(
                route('mypanel'),
                $faGear, 
                '',
                [],
                true,
                'Meu painel'
            );
        }else{
            $menu->addWithIcon(
                route('certifyVerify'),
                $faSearch,
                'Verificar certificado',
                [],
                true
            );

            $loginRoute = 'login-panel';
            $loginSetting = Setting::where(['key' => 'default_login'])->first();

            if(!empty($loginSetting) && $loginSetting->value == 'oauth2') {
                $loginRoute = 'login';
            }

            $menu->add(
                route($loginRoute), 
                'Fazer Login', 
                ['button', 'button-primary', 'bt-login']
            );
        }

        $request->merge([$mn => $menu->render()]);

        return $request;
    }

    /**
     * Get html code for social media menu.
     *
     * @param Request $request
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @return Request
     */
    protected function _getSocialMenu($request, $type = '', $menuname = '', $menuclass = []) {
        $mn = $menuname;
        $mt = $type;
        $mc = $menuclass;
        $menu = new Menu($mt, $mc);

        $youtubeChannelUrlSetting = Setting::where(['key' => 'youtube_channel_url'])->first();
        $youtubeChannelUrl = empty($youtubeChannelUrlSetting) ? '' : $youtubeChannelUrlSetting->value;

        if (!empty($youtubeChannelUrl)) {
            $menu->addWithIcon(
                $youtubeChannelUrl,
                ['fa', 'fa-youtube-play', 'size-g'],
                '',
                [],
                true
            );
        }

        $socialMedias = [
            [
                'media' => 'social_media_facebook',
                'url_base' => 'https://www.facebook.com/',
                'icon' => ['fa', 'fa-facebook', 'size-g'],
                '',
                [],
                true
            ],

            [
                'media' => 'social_media_instagram',
                'url_base' => 'https://www.instagram.com/',
                'icon' => ['fa', 'fa-instagram', 'size-g'],
                '',
                [],
                true
            ],

            [
                'media' => 'social_media_twitter',
                'url_base' => 'https://www.twitter.com/',
                'icon' => ['fa', 'fa-twitter', 'size-g'],
                '',
                [],
                true
            ],
        ];

        foreach($socialMedias as $socialMedia) {

            $socialMediaSetting = Setting::where(['key' => $socialMedia['media']])->first();
            $socialMediaUser = empty($socialMediaSetting) ? '' : $socialMediaSetting->value;
            if (!empty($socialMediaUser)) {
                $menu->addWithIcon(
                    $socialMedia['url_base'] . $socialMediaUser,
                    $socialMedia['icon'],
                    '',
                    [],
                    true
                );
            }
        }

        $request->merge([$mn => $menu->render()]);

        return $request;
    }

    /**
     * Get html code for menu on user's panel page.
     *
     * @param Request $request
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @return Request
     */
    protected function getUserControllerMenu($request,  $type = '', $menuname = '', $menuclass = []) {
        $user = Auth::user();
        $role = new Role();
        $mn = $menuname;
        $mt = $type;
        $mc = $menuclass;
        $menu = new Menu($mt, $mc);

        $menu->add(
            route('userShow', ['user' => $user->id]), 
            'Meus dados', 
            $request->route()->getName() == 'userShow' ? ['is-active'] : []
        );

        $menuItems = [
            ['route' => 'certifies', 'text' => $user->isAdmin() ? 'Certificados' : 'Meus Certificados'],
            ['route' => 'userDestroyConfirmation', 'text' => 'Remover conta'],
        ];

        foreach ($menuItems as $item) {
            $menu->add(
                route($item['route']), 
                $item['text'], 
                $request->route()->getName() == $item['route'] ? ['is-active'] : []
            );
        }

        if (!$user->isStudent()) {
            $menu->add(
                route('courseCreate'), 
                'Novo curso',
                $request->route()->getName() == 'courseCreate' ? ['is-active'] : []
            );
        }

        if ($user->isAdmin()) {
            $menu->add(
                route('users'), 
                'Usuários', 
                $request->route()->getName() == 'users' ? ['is-active'] : []
            );
            $menu->add(
                route('settings'), 
                'Configurações',
                $request->route()->getName() == 'settings' ? ['is-active'] : []
            );
        }

        $menu->add(route('logout'), 'Sair');

        $pagetitle = 'Meu painel';

        $request->merge(['pagetitle' => $pagetitle, $mn => $menu->render()]);

        return $request;
    }

    /**
     * Get html code for menu on course page.
     *
     * @param Request $request
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @return Request
     */
    protected function getCourseControllerMenu($request,  $type = '', $menuname = '', $menuclass = []) {
        $user = Auth::user();
        $course = $request->route('course');

        if (!empty($course)) {
            $certifyLink = $this->_getCertifyLink($course, $user);
            $html = $this->_getSideBarMenu($course, $user, $type, $menuname, $menuclass);
            $pagetitle = $course->title;
            $request->merge(['pagetitle' => $pagetitle, $menuname => $html, 'certifyLink' => $certifyLink]);
        }

        return $request;
    }
    
    /**
     * Get html code for menu on lesson page.
     *
     * @param Request $request
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @return Request
     */
    protected function getLessonControllerMenu($request,  $type = '', $menuname = '', $menuclass = []) {
        $user = Auth::user();
        $lesson = $request->route('lesson');

        if (!empty($lesson)) {
            $course = $lesson->module()->course();
            $certifyLink = $this->_getCertifyLink($course, $user);
            $html = $this->_getSideBarMenu($course, $user, $type, $menuname, $menuclass, $lesson);
            $pagetitle = $lesson->title;
            $request->merge(['pagetitle' => $pagetitle, $menuname => $html, 'certifyLink' => $certifyLink]);
        }
        
        return $request;
    }

    /**
     * Get html code for menu on activity page.
     *
     * @param Request $request
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @return Request
     */
    protected function getActivityControllerMenu($request,  $type = '', $menuname = '', $menuclass = []) {
        $user = Auth::user();
        $activity = $request->route('activity');

        if (!empty($activity)) {
            $course = $activity->module()->course();
            $certifyLink = $this->_getCertifyLink($course, $user);
            $html = $this->_getSideBarMenu($course, $user, $type, $menuname, $menuclass, $activity);
            $pagetitle = $activity->title;
            $request->merge(['pagetitle' => $pagetitle, $menuname => $html, 'certifyLink' => $certifyLink]);
        }

        return $request;
    }

    /**
     * Get html code for menu on certify page.
     *
     * @param Request $request
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @return Request
     */
    protected function getCertifyControllerMenu($request,  $type = '', $menuname = '', $menuclass = []) {
        if (Auth::check())
            return $this->getUserControllerMenu($request,  $type, $menuname, $menuclass);
        else
            return $request;
    }

    /**
     * Get html code for menu on setting page.
     *
     * @param Request $request
     * @param string $type
     * @param string $menuname
     * @param array $menuclass
     * @return Request
     */
    protected function getSettingControllerMenu($request,  $type = '', $menuname = '', $menuclass = []) {
        if (Auth::check())
            return $this->getUserControllerMenu($request,  $type, $menuname, $menuclass);
        else
            return $request;
    }
}
