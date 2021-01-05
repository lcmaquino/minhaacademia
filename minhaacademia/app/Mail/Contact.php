<?php

namespace App\Mail;

use App\Setting;
use App\ContactForm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    public $contactForm;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ContactForm $contactForm)
    {
        $this->contactForm = $contactForm;
    }

    /**
     * Build the message.
     *
     * @return Contact
     */
    public function build()
    {
        $appNameSetting = Setting::where(['key' => 'app_name'])->first();
        $appName = empty($appNameSetting) ? '' : $appNameSetting->value;

        $subject = '[Contato] ' . $appName;

        return $this->replyTo($this->contactForm->email, $this->contactForm->name)
            ->subject($subject)
            ->view('emails.contact', ['appName' => $appName]);
    }
}
