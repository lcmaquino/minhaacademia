<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueCPF implements Rule
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
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (strlen($value) != 14) {
            $cpf = null;
        }else{
            $cpf = substr($value, 0, 3) . substr($value, 4, 3) . substr($value, 8, 3) . substr($value, -2);
        }

        if (empty($this->user)) {   
            return ($cpf == null || User::where(['cpf' => $cpf])->first() == null);
        }else{
            return ($cpf == null || (User::where(['cpf' => $cpf, ['id', '<>', $this->user->id]])->first() == null));
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
