<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Setting::class);
        $settings = Setting::all();
        $param = [];

        foreach($settings as $setting) {
            $key = Str::camel($setting->key);
            if ($key == 'mailPassword') {
                $param['isPasswordEmpty'] = empty($setting->getValue());
            }else{
                $param[$key] = $setting->getValue();
            }
        }

        return view('admin.settings', $param);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->authorize('update', Setting::class);
        $this->validateSettings($request);

        $settings = Setting::all();

        foreach ($settings as $setting) {
            $key = Str::camel($setting->key);
            if($request->has($key)) {
                $setting->setValue($request->input($key));
                $setting->save();
            }
        }

        return redirect()->route('settings');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clearUserMailPassword(Request $request)
    {
        $this->authorize('update', Setting::class);

        $setting = Setting::where(['key' => 'mail_password'])->first();

        if (!empty($setting)) {
            $setting->value = null;
            $setting->save();            
        }

        return redirect()->route('settings');
    }

    /**
     * Validate form for update settings.
     *
     * @param Request $request
     * @return void
     */
    public function validateSettings(Request $request){
        $rules = [
            'appName' => ['required', 'string', 'max:512'],
            'appUrl' => ['required', 'url', 'max:512'],
            'appContactMail' => ['nullable', 'email', 'max:512'],
            'minScore' => ['required', 'integer', 'min:0', 'max:100'],
            'defaultLogin' => ['required', 'in:form,oauth2'],
            'googleClientId' => ['nullable', 'string', 'max:512'],
            'googleClientSecret' => ['nullable', 'string', 'max:512'],
            'googleRedirectUri' => ['nullable', 'url', 'max:512'],
            'donationUrl' => ['nullable', 'url', 'max:512'],
            'certifySignatureName' => ['required', 'string', 'max:512'],
            'certifyState' => ['required', 'string', 'max:512'],
            'mailHost' => ['nullable', 'string', 'max:512'],
            'mailPort' => ['nullable', 'integer', 'min:0'],
            'mailUsername' => ['nullable', 'email', 'max:512'],
            'mailPassword' => ['nullable', 'string', 'max:512'],
            'mailEncryption' => ['nullable', 'in:tls,ssl'],
            'mailFromAddress' => ['nullable', 'email', 'max:512'],
            'mailFromName' => ['nullable', 'string', 'max:512'],
            'mailToAddress' => ['nullable', 'email', 'max:512'],
            'youtubeChannelId' => ['required', 'string', 'max:512'],
            'youtubeChannelTitle' => ['required', 'string', 'max:512'],
            'youtubeChannelUrl' => ['required', 'url', 'max:512'],
            'youtubeChannelDefaultVideo' => ['required',  'size:11', 'regex:/[a-z0-9_-]{11}/i'],
            'socialMediaFacebook' => ['nullable', 'regex:/[a-z0-9\.]{1,255}/i'],
            'socialMediaInstagram' => ['nullable', 'regex:/[a-z0-9_\.]{1,30}/i'],
            'socialMediaTwitter' => ['nullable', 'regex:/[a-z0-9_]{1,255}/i'],
            'googleRecaptchaSiteKey' => ['nullable', 'string', 'max:512'],
            'googleRecaptchaSecretKey' => ['nullable', 'string', 'max:512'],
        ];

        $error_messages = [
            'appName.required' => 'É obrigatório informar um :attribute',
            'appName.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'appName.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'appUrl.required' => 'É obrigatório informar um :attribute',
            'appUrl.url' => 'O :attribute deve ser um URL válido',
            'appUrl.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'appContactMail.email' => 'O :attribute deve ser um e-mail válido',
            'appContactMail.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'minScore.required' => 'É obrigatório informar uma :attribute',
            'minScore.integer' => 'A :attribute deve ser um inteiro entre 0 e 100',
            'minScore.min' => 'A :attribute deve ser um inteiro entre 0 e 100',
            'minScore.max' => 'A :attribute deve ser um inteiro entre 0 e 100',
            'defaultLogin.required' => 'É obrigatório informar um :attribute',
            'defaultLogin.in' => 'O :attribute deve ser Formulário ou OAuth 2.0',
            'googleClientId.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'googleClientId.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'googleClientSecret.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'googleClientSecret.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'googleRedirectUri.url' => 'O :attribute deve ser um URL válido',
            'googleRedirectUri.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'donationUrl.url' => 'O :attribute deve ser um URL válido',
            'donationUrl.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'certifySignatureName.required' => 'É obrigatório informar um :attribute',
            'certifySignatureName.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'certifySignatureName.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'certifyState.required' => 'É obrigatório informar um :attribute',
            'certifyState.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'certifyState.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'mailHost.string' => 'O :attribute deve ser um URL válido',
            'mailHost.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'mailPort.integer' => 'A :attribute deve ser um número inteiro',
            'mailPort.min' => 'A :attribute deve ser maior ou igual a 0',
            'mailUsername.email' => 'A :attribute deve ser um e-mail válido',
            'mailUsername.max' => 'A :attribute deve ter no máximo 512 caracteres',
            'mailPassword.string' => 'A :attribute deve ser formada por caracteres alfanuméricos',
            'mailPassword.max' => 'A :attribute deve ter no máximo 512 caracteres',
            'mailEncryption.in' => 'A :attribute deve ser LS ou SSL',
            'mailFromAddress.email' => 'O :attribute deve ser um e-mail válido',
            'mailFromAddress.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'mailFromName.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'mailFromName.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'mailToAddress.email' => 'O :attribute deve ser um e-mail válido',
            'mailToAddress.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'youtubeChannelId.required' => 'É obrigatório informar um :attribute',
            'youtubeChannelId.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'youtubeChannelId.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'youtubeChannelTitle.required' => 'É obrigatório informar um :attribute',
            'youtubeChannelTitle.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'youtubeChannelTitle.max' => 'O :attribute deve ter no máximo 512 caracteres',
            'youtubeChannelUrl.required' => 'É obrigatório informar uma :attribute',
            'youtubeChannelUrl.url' => 'A :attribute deve ser um URL válido',
            'youtubeChannelUrl.max' => 'A :attribute deve ter no máximo 512 caracteres',
            'youtubeChannelDefaultVideo.required' => 'É obrigatório informar um :attribute',
            'youtubeChannelDefaultVideo.size' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _',
            'youtubeChannelDefaultVideo.regex' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _',
            'socialMediaFacebook.regex' => 'O :attribute deve ter no máximo 512 caracteres, com apenas letras, dígitos ou .',
            'socialMediaInstagram.regex' => 'O :attribute deve ter no máximo 30 caracteres, com apenas letras, dígitos, _ ou .',
            'socialMediaTwitter.regex' => 'O :attribute deve ter no máximo 30 caracteres, com apenas letras, dígitos ou _',
            'googleRecaptchaSiteKey.string' => 'A :attribute deve ser formado por caracteres alfanuméricos',
            'googleRecaptchaSiteKey.max' => 'A :attribute deve ter no máximo 512 caracteres',
            'googleRecaptchaSecretKey.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'googleRecaptchaSecretKey.max' => 'O :attribute deve ter no máximo 512 caracteres',
        ];

        $attributes = [
            'appName' => 'nome da aplicação',
            'appUrl' => 'Url da aplicação',
            'appContactMail' => 'e-mail de contato',
            'minScore' => 'pontuação mínima',
            'defaultLogin' => 'login padrão',
            'googleClientId' => 'Google Client ID',
            'googleClientSecret' => 'Google Client Secret',
            'googleRedirectUri' => 'Google Redirect URI',
            'donationUrl' => 'Url para doação',
            'certifySignatureName' => 'Nome na assinatura',
            'certifyState' => 'local (Estado)',
            'mailHost' => 'host do serviço de e-mail',
            'mailPort' => 'porta do serviço de e-mail',
            'mailUsername' => 'conta do usuário',
            'mailPassword' => 'senha do usuário',
            'mailEncryption' => 'criptografia',
            'mailFromAddress' => 'e-mail do remetente',
            'mailFromName' => 'nome do remetente',
            'mailToAddress' => 'e-mail do destinatário',
            'youtubeChannelId' => 'ID do canal',
            'youtubeChannelTitle' => 'título do canal',
            'youtubeChannelUrl' => 'URL do canal',
            'youtubeChannelDefaultVideo' => 'vídeo padrão',
            'socialMediaFacebook' => 'perfil/página do facebook',
            'socialMediaInstagram' => 'perfil do instagram',
            'socialMediaTwitter' => 'perfil do twitter',
            'googleRecaptchaSiteKey' => 'chave do site',
            'googleRecaptchaSecretKey' => 'segredo do site',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }
}