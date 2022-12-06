<?php

namespace Applyflow\LaravelScreenQuestions\Traits;

use Applyflow\LaravelScreenQuestions\Exceptions\FieldDoesNotBelongToModelException;
use Applyflow\LaravelScreenQuestions\Exceptions\WrongNumberOfFieldsForOrderingException;
use Applyflow\LaravelScreenQuestions\Models\ScreenQuestion;
use Applyflow\LaravelScreenQuestions\Validators\ScreenQuestionValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait HasScreenQuestions
{
    /**
     * Get the custom fields belonging to this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function screenQuestions($group = null)
    {
        $rel = $this->morphMany(ScreenQuestion::class, 'model')->where('group', $group);
        return $rel;
    }

    public function allScreenQuestions()
    {
        $rel = $this->morphMany(ScreenQuestion::class, 'model')->orderBy('group');
        return $rel;
    }

    /**
     * Validate the given custom fields.
     *
     * @param $fields
     * @return ScreenQuestionValidator
     */
    public function validateScreenQuestions($fields)
    {
        $validationRules = $this->allScreenQuestions()->get()->mapWithKeys(function ($field) {
            return ['field_' . $field->id => $field->validationRules];
        })->toArray();

        $keyAdjustedFields = collect($fields)
            ->mapWithKeys(function ($field, $key) {
                return ["field_{$key}" => $field];
            })->toArray();

        return new ScreenQuestionValidator($keyAdjustedFields, $validationRules);
    }

    public function validateScreenQuestion($question_id, $value)
    {
        $field = $this->allScreenQuestions()->where('id', $question_id)->first();

        if (!$field) {
            throw new FieldDoesNotBelongToModelException($question_id, $this);
        }

        $validationRules = [
            "field_{$question_id}" => $field->validationRules,
        ];

        $keyAdjustedFields = [
            "field_{$question_id}" => $value
        ];

        return Validator::make($keyAdjustedFields, $validationRules);
    }

    /**
     * Validate the given custom field request.
     *
     * @param Request $request
     * @return ScreenQuestionValidator
     */
    public function validateScreenQuestionsRequest(Request $request)
    {
        return $this->validateScreenQuestions($request->get(config('screen-questions.form_name', 'custom_fields')));
    }
}
