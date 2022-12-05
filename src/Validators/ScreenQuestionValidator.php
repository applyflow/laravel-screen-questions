<?php

namespace Applyflow\LaravelScreenQuestions\Validators;

use Applyflow\LaravelScreenQuestions\Models\ScreenQuestion;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class ScreenQuestionValidator extends Validator
{
    /**
     * Create a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules)
    {
        parent::__construct(
            app('translator'),
            $data,
            $rules
        );
    }

    /**
     * Replace the :attribute placeholder in the given message.
     *
     * @param string $message
     * @param string $value
     * @return string
     */
    protected function replaceAttributePlaceholder($message, $value)
    {
        $fieldId = (int) Str::after($value, 'field ');
        $fieldTitle = ScreenQuestion::find($fieldId)->title;
        $replacementString = "`{$fieldTitle}` field";

        return str_replace(
            [':attribute', ':ATTRIBUTE', ':Attribute'],
            [$replacementString, Str::upper($replacementString), Str::ucfirst($replacementString)],
            $message
        );
    }
}
