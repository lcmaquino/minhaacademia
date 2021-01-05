<?php

namespace App\Http\Middleware;

use App\Setting;
use MyYouTubeChannel;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
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
        $info = 'Ops! No momento alguns dados 
            estão indisponíveis. Por isso, as informações exibidas podem 
            estar incorretas. Nova tentativa em 10 segundos.';
        $loginSetting = Setting::getSetting('default_login');

        if (!empty($user) && !empty($loginSetting) && $loginSetting === 'oauth2') {
            if ($user->isStudent()) {
                $user->refreshTokenIfNeeded();
                $subscribed = MyYouTubeChannel::isUserSubscribed($user->access_token);

                if ($subscribed === null) {
                    $request->merge(['googleapi' => $info]);
                    $response = $next($request);
                    $response->headers->set('refresh', '10');
                    return $response;
                }

                if(!$subscribed){
                    return redirect()->route('logout');
                }
            }
        }

        return $next($request);
    }
}
