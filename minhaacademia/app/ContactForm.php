<?php

namespace App;

class ContactForm
{
    public $name = null;
    public $email = null;
    public $message = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name = '', $email = '', $message = '')
    {
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
    }

}