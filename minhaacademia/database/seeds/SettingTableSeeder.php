<?php

use Illuminate\Database\Seeder;

use App\Setting;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            'app_name' => 'Minha Academia Aberta',
            'app_url' => 'https://localhost/',
            'app_author' => 'Nome do(a) Professor(a)',
            'app_description' => 'Descrição resumida do seu site.',
            'app_contact_mail' => 'contato@localhost',
            'default_login' => 'oauth2',
            'min_score' => '75',
            'mail_mailer' => 'smtp',
            'mail_host' => null,
            'mail_port' => '465',
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => 'ssl',
            'mail_from_address' => null,
            'mail_from_name' => '',
            'mail_to_address' => null,
            'donation_url' => null,
            'certify_signature_name' => 'Nome do(a) Professor(a)',
            'certify_state' => 'Estado',
            'youtube_channel_id' => null,
            'youtube_channel_title' => null,
            'youtube_channel_url' => null,
            'youtube_channel_default_video' => null,
            'social_media_facebook' => null,
            'social_media_instagram' => null,
            'social_media_twitter' => null,
            'google_client_id' => null,
            'google_client_secret' => null,
            'google_redirect_uri' => null,
            'google_recaptcha_site_key' => null,
            'google_recaptcha_secret_key' => null,
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}
