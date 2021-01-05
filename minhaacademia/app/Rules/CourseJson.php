<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CourseJson implements Rule
{

    protected $message = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($message = '')
    {
        $defaultMessage = 'Arquivo inválido';
        $this->message = empty($message) ? $defaultMessage : $message;
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
        if ($value->getClientMimeType() == 'application/json') {
            $data = json_decode($value->get());

            if(isset($data->title) && isset($data->duration) && isset($data->visibility)
                && filter_var($data->duration, FILTER_VALIDATE_INT) !== false
                && filter_var($data->visibility, FILTER_VALIDATE_INT) !== false
                && ($data->visibility == 0 || $data->visibility == 1)
                && count($data->modules) > 0) {
                foreach($data->modules as $module) {
                    if (empty($module->title) 
                        || filter_var($module->order, FILTER_VALIDATE_INT) === false
                        || empty($module->lessons)) {
                        return false;
                    }

                    foreach ($module->lessons as $lesson) {
                        if (empty($lesson->title) || filter_var($lesson->order, FILTER_VALIDATE_INT) === false) {
                            return false;
                        }
                    }
                    
                    if(!empty($module->activities)) {
                        foreach ($module->activities as $activity) {
                            if (empty($activity->title) 
                                || filter_var($activity->order, FILTER_VALIDATE_INT) === false
                                || empty($activity->questions)) {
                                return false;
                            }

                            foreach ($activity->questions as $question) {
                                if (empty($question->content)
                                    || filter_var($question->answer, FILTER_VALIDATE_INT) === false
                                    || filter_var($question->order, FILTER_VALIDATE_INT) === false
                                    || empty($question->items)) {
                                    return false;
                                }

                                foreach ($question->items as $item) {
                                    if (empty($item->content)
                                        || filter_var($item->order, FILTER_VALIDATE_INT) === false) {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
                return true;
            }
        }
        return false;
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
