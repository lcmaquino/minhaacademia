<?php

namespace App\Rules;

use App\Course;
use App\Module;
use App\Activity;
use App\Question;
use Illuminate\Contracts\Validation\Rule;

class ModelExists implements Rule
{
    protected $className;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($className = '', $message ='')
    {
        $this->className = $className;
        $this->message = $message;
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
        $model = null;

        if (class_exists($this->className)) {
            $model = call_user_func($this->className . '::find', $value);
        }

        return (!empty($model));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return empty($this->message) ? '' : $this->message;
    }
}
