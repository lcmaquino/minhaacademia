<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'value',
    ];

    /**
     * The keys with encrypted value.
     *
     * @var array
     */
    protected $encrypt = [
        'mail_password',
        'google_client_secret',
        'google_recaptcha_secret_key',
    ];

    /**
     * Set the value for some key. It will encrypt $value if needed.
     *
     * @param string $value
     */
    public function setValue($value) {
        if ($value !== null && in_array($this->key, $this->encrypt)) {
            $this->value = Crypt::encryptString($value);
        }else{
            $this->value = $value;
        }
    }

    /**
     * Get the value for some key. It will decrypt $value if needed.
     *
     * @return string
     */
    public function getValue(){
        $decryptedValue = null;

        if ($this->value !== null && in_array($this->key, $this->encrypt)) {
            try {
                $decryptedValue = Crypt::decryptString($this->value);
            } catch (DecryptException $e) {
                ;
            }
        }else{
            $decryptedValue = $this->value;
        }

        return $decryptedValue;
    }

    /**
     * Get the setting with key equals to $key.
     *
     * Returns null when $key setting does not existis or it's value is null.
     * 
     * @param string $key
     * @return string|null
     */
    static public function getSetting($key = '') {
        $s = Setting::where(['key' => $key])->first();
        return empty($s) ? null : $s->getValue();
    }

    /**
     * Get the setting with key in array $keys.
     * 
     * Returns the settings as an associative array 
     * like [$key => $value].
     
     * If $strCamel is true, then $key will be returned 
     * in camel format.
     * 
     * If $prefix is given, then it will get the setting
     * with key equals to $prefix . $key.
     * 
     * If a key does not existis, then its value will 
     * be set as null.
     * 
     * @param array $key
     * @param string $prefix
     * @param boolean $strCamel
     * @return array
     */
    static public function getSettingArray($keys = [], $prefix = '', $strCamel = false) {
        $settings = [];
        foreach ($keys as $key) {
            $keyCamel = $strCamel ? Str::camel($key) : $key;
            $settings[$keyCamel] = Setting::getSetting($prefix . $key);
        }
        return $settings;
    }

    /**
     * Get the OAuth 2.0 client setting: client_id, client_secret, and
     * redirect_uri.
     * 
     * @return array
     */
    static public function getOAuth2Config(){
        return Setting::getSettingArray([
            'client_id',
            'client_secret',
            'redirect_uri',
        ], 'google_');
    }

    /**
     * Get YouTube channel setting: channel_id, channel_title, and
     * channel_url.
     * 
     * @return void
     */
    static public function getYouTubeChannelSetting(){
        return Setting::getSettingArray([
            'channel_id',
            'channel_title',
            'channel_url',
            'channel_default_video',
            'channel_about',
        ], 'youtube_');
    }
}