<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use App\Models\SettingManager;
use Lcmaquino\YouTubeChannel\YouTubeChannelManager;
use MyYouTubeChannel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public $page_info = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the Google login page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showLoginGoogle(Request $request)
    {
        // Get URLs
        $urlPrevious = url()->previous();
        $urlBase = url()->to('/');

        // Set the previous url that we came from to redirect to after successful login but only if is internal
        if(($urlPrevious != $urlBase . '/login') && (substr($urlPrevious, 0, strlen($urlBase)) === $urlBase)) {
            session()->put('url.intended', $urlPrevious);
        }

        $approvalPrompt = $request->query('approval_prompt');
        $channelUrl = Setting::getSetting('youtube_channel_url');
        $channelTitle = Setting::getSetting('youtube_channel_title');

        return view('auth.loginGoogle', ['approvalPrompt' => $approvalPrompt, 'channelUrl' => $channelUrl, 'channelTitle' => $channelTitle]);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        // Get URLs
        $urlPrevious = url()->previous();
        $urlBase = url()->to('/');

        // Set the previous url that we came from to redirect to after successful login but only if is internal
        if(($urlPrevious != $urlBase . '/login') && (substr($urlPrevious, 0, strlen($urlBase)) === $urlBase)) {
            session()->put('url.intended', $urlPrevious);
        }

        return view('auth.login');
    }

    /**
     * Make login from form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){
        $this->validateLogin($request);

        $credentials = $request->only('email', 'password');
        $rememberMe = true;

        if (Auth::attempt($credentials, $rememberMe)){
            return redirect()->intended();
        }else{
            $errors = new MessageBag();
            $errors->add('email', 'login inválido');
            return redirect()->route('login-panel')->withErrors($errors);
        }
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ],[
            'email.required' => ':attribute inválido',
            'password.required' => ':attribute inválida',
        ], [
            'email' => 'e-mail',
            'password' => 'senha',
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => ['Usuário ou senha inválido.'],
        ]);
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->intended();
    }

    /**
     * Google authentication redirect.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function providerRedirect(Request $request){
        $approval_prompt = $request->query('approval_prompt');
        $approval_prompt = in_array($approval_prompt, ['auto', 'force'], true) ? $approval_prompt : 'auto';
        $params = [
            'approval_prompt' => $approval_prompt,
            'access_type' => 'offline',
        ];

        return MyYouTubeChannel::with($params)->redirect();
    }

    /**
     * Handle Google OAuth 2.0 authentication callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request) {
        $ytuser = MyYouTubeChannel::user();

        if(empty($ytuser)){
            $channelTitle = Setting::getSetting('youtube_channel_title');

            return $this->sendGoogleAPIFailedResponse(
                'login',
                'googleapi',
                'Ops! :/ Não foi possível verificar se você está inscrito no canal ' . $channelTitle . '. Tente novamente mais tarde.'
            );
        }else{
            $subscribed = MyYouTubeChannel::isUserSubscribed();
            if($subscribed === null){
                return $this->sendGoogleAPIFailedResponse(
                    'login',
                    'googleapi',
                    'Ops! :/ Não foi possível verificar se você está inscrito no canal ' . $channelTitle . '. Tente novamente mais tarde.'
                );
            }

            $user = User::findByEmail($ytuser->email);
            if(empty($user)){
                if($subscribed){
                    $user = User::store($ytuser);
                }else{
                    MyYouTubeChannel::revokeToken($ytuser->token);
                }
            }else{
                if(empty($user->refresh_token) && empty($ytuser->refreshToken)) {
                    return $this->sendGoogleAPIFailedResponse(
                        'login',
                        'googleapi',
                        'Ops! :/ Não foi possível efetuar o login. Tente novamente mais tarde.',
                        ['approval_prompt' => 'force']
                    );
                }

                $user->updateToken([
                    'token' => $ytuser->token,
                    'refreshToken' => $ytuser->refreshToken,
                    'expiresIn' => $ytuser->expiresIn,
                ]);
            }

            if($subscribed || (!empty($user) && !$user->isStudent())){
                $rememberMe = true;
                Auth::login($user, $rememberMe);
            }else{
                $channelTitleSetting = Setting::where(['key' => 'youtube_channel_title'])->first();
                $channelTitle = empty($channelTitleSetting) ? '' : $channelTitleSetting->value;
                $msg = 'Você precisa inscrever-se no canal ' . $channelTitle
                    . ' para ter acesso ao site.';
                return $this->sendGoogleAPIFailedResponse('login', 'subscription', $msg);
            }
        }

        return redirect()->intended();
    }

    /**
     * Show Google login errors.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendGoogleAPIFailedResponse($route, $key, $value, $param = []){
        $errors = new MessageBag();
        $errors->add($key, $value);
        return redirect()->route($route, $param)->withErrors($errors);
    }
}