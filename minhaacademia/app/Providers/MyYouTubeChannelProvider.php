<?php

namespace App\Providers;

use App\Setting;
use Lcmaquino\YouTubeChannel\YouTubeChannelManager;
use Illuminate\Support\ServiceProvider;

class MyYouTubeChannelProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('MyYouTubeChannel', function($app){
            $config = Setting::getOAuth2Config();
            $config['youtube_channel_id'] = Setting::getSetting('youtube_channel_id');
            return (new YouTubeChannelManager($config, $app->request));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return 'MyYouTubeChannel';
    }
}