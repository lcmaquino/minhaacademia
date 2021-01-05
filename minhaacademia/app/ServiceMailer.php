<?php

namespace App;

use Illuminate\Mail\Mailer;
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_Message;

class ServiceMailer {

    protected $settings = [];
    
    /**
     * Constructor for ServiceMailer class.
     * @param array $settings
     */
    public function __construct($settings = [ 'mail_host' => '', 'mail_port' => '', 'mail_username' => '', 'mail_password' => '', 'mail_encryption' => '', 'mail_from_address' => '', 'mail_from_name' => '',]){
        $this->settings = $settings;
    }

    /**
     * Get a mailer.
     *
     * @return Mailer
     */
    public function getMailer(){
        $transport = new Swift_SmtpTransport($this->settings['mail_host'], $this->settings['mail_port']);
        $transport->setUsername($this->settings['mail_username']);
        $transport->setPassword($this->settings['mail_password']);
        $transport->setEncryption($this->settings['mail_encryption']);
        
        $swiftMailer = new Swift_Mailer($transport);
      
        $view = app()->get('view');
        $events = app()->get('events');

        $mailer = new Mailer($this->settings['mail_from_name'], $view, $swiftMailer, $events);
        $mailer->alwaysFrom($this->settings['mail_from_address'], $this->settings['mail_from_name']);
 
        return $mailer;
    }
}