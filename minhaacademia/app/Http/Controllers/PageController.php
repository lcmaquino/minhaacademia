<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Certify;
use App\Models\LessonProgress;
use App\Models\ActivityProgress;
use App\ContactForm;
use App\Filter;
use App\ServiceMailer;
use App\Mail\Contact;
use MyYouTubeChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PageController extends Controller
{
    /**
     * Process contact form response.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     * 
     * @throws Exception
     */
    public function contactSend(Request $request)
    {
        $this->validateContact($request);
        $googleRecaptchaSecretKey = Setting::getSetting('google_recaptcha_secret_key');
        $contactStatus = null;

        if(!empty($googleRecaptchaSecretKey)) {
            $googleRecaptchaURL = 'https://www.google.com/recaptcha/api/siteverify';
            $query = http_build_query([
                'secret' => $googleRecaptchaSecretKey,
                'response' => $request->recaptcha_response,            
            ]);
            
            $googleRecaptcha = Http::get($googleRecaptchaURL . '?' . $query)->json();
        }

        if (empty($googleRecaptchaSecretKey) ||
             ($googleRecaptcha['success'] 
               && $googleRecaptcha['hostname'] === $request->getHttpHost()
               && $googleRecaptcha['action'] === 'contact')) {
                $settings = Setting::getSettingArray([
                    'mail_host',
                    'mail_port',
                    'mail_username',
                    'mail_password',
                    'mail_encryption',
                    'mail_from_address',
                    'mail_from_name',
                ]);
                $mailToAddress = Setting::getSetting('mail_to_address');
                $contactForm = new ContactForm($request->name, $request->email, $request->message);
                $serviceMailer = new ServiceMailer($settings);
                $mailer = $serviceMailer->getMailer();

                try {
                    $mailer->to($mailToAddress)->send(new Contact($contactForm));
                    $contactStatus = 'Mensagem enviada!';
                    $request->session()->flash('contactStatus', ['status' => 'info', 'message' => $contactStatus]);
                } catch (\Exception $e) {
                    $contactStatus = 'Ops! :/ Mensagem não enviada. Tente novamente mais tarde.';
                    $request->session()->flash('contactStatus', ['status' => 'danger', 'message' => $contactStatus]);
                }
        }else{
            $contactStatus = 'Ops! :/ Mensagem não enviada. Tente novamente mais tarde.';
            $request->session()->flash('contactStatus', ['status' => 'danger', 'message' => $contactStatus]);
        }

        return redirect()->route('contact');
    }

    /**
     * Validate the contact request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateContact(Request $request)
    {
        $required = 'Campo :attribute obrigatório.';

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|string',
            'message' => 'required|string',
        ],[
            'name.required' => $required,
            'email.required' => $required,
            'email.email' => 'formato do :attribute inválido',
            'message.required' => $required,
        ],
        [
            'name' => 'nome',
            'email' => 'e-mail',
            'message' => 'mensagem',
        ]);
    }

    /**
     * Display the contact page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact(){
        $googleRecaptchaSiteKey = Setting::getSetting('google_recaptcha_site_key');

        return view('site.contact', ['googleRecaptchaSiteKey' => $googleRecaptchaSiteKey]);
    }

    /**
     * Display information about login.
     *
     * @return \Illuminate\Http\Response
     */
    public function aboutLogin(){
        $settings = Setting::getSettingArray([
            'app_url',
            'youtube_channel_title',
            'youtube_channel_url'
        ],'', true);

        return view('site.aboutLogin', $settings);
    }

    /**
     * Display information about special tags for formatting content.
     *
     * @return \Illuminate\Http\Response
     */
    public function format(){
        return view('site.format', ['filter' => new Filter()]);
    }

    /**
     * Display the terms of service page.
     *
     * @return \Illuminate\Http\Response
     */
    public function terms(){
        $settings = Setting::getSettingArray([
            'app_name',
            'app_contact_mail'
        ], '', true);

        return view('site.terms', $settings);
    }

    /**
     * Display the privacy page.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy(){
        $settings = Setting::getSettingArray([
            'app_name',
            'app_contact_mail'
        ], '', true);

        return view('site.privacy', $settings);
    }

    /**
     * Display the about page.
     *
     * @return \Illuminate\Http\Response
     */
    public function about(){
        $courseStatistics = [
            'certifiesCount' => Certify::count(),
            'lessonProgressCount' => LessonProgress::count(),
            'activityProgressCount' => ActivityProgress::count(),
        ];
        $user = Auth::user() ? Auth::user() : User::firstAdmin();
        $user->refreshTokenIfNeeded();
        $channelStatistics = $user ? MyYouTubeChannel::getChannelStatistics($user->access_token) : null;
        $info = Setting::getSettingArray(['youtube_channel_default_video', 'youtube_channel_about'], '', true);
        $info['youtubeChannelAbout'] = isset($info['youtubeChannelAbout']) ? 
            (new Filter($info['youtubeChannelAbout']))->render() : '';
        $info['courseStatistics'] = $courseStatistics;
        $info['channelStatistics'] = $channelStatistics;

        return view('site.about', $info);
    }
}