<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueEmail implements Rule
{
    protected $user;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user = null, $message = null)
    {
        $this->user = $user;
        $this->message = empty($message) ? 'Este :attribute já está cadastrado' : $message;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        $email = filter_var($value, FILTER_VALIDATE_EMAIL);
        if (empty($this->user)) {
            return ($email !== false && User::where(['email' => $email])->first() == null);
        }else{
            return ($email !== false && User::where(['email' => $email, ['id', '<>', $this->user->id]])->first() == null);
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
