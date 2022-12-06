<?php

namespace Applyflow\LaravelScreenQuestions\Traits;

use Applyflow\LaravelScreenQuestions\Models\ScreenQuestion;
use Applyflow\LaravelScreenQuestions\Models\ScreenQuestionResponse;

trait HasScreenQuestionResponses
{
    /**
     * Get the custom field responses for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function screenQuestionResponses()
    {
        return $this->morphMany(ScreenQuestionResponse::class, 'model');
    }

    /**
     * Save the given custom fields to the model.
     *
     * @param $fields
     */
    public function saveScreenQuestions($fields)
    {
        foreach ($fields as $key => $value) {
            $screenQuestion = ScreenQuestion::find((int) $key);

            if (!$screenQuestion) {
                continue;
            }

            if ($screenQuestion->type == ScreenQuestion::TYPE_MULTISELECT) {
                ScreenQuestionResponse::where(
                    [
                        'model_id' => $this->id,
                        'question_id' => $screenQuestion->id,
                        'model_type' => $this->getMorphClass(),
                    ]
                )->delete();

                if (is_array($value)) {
                    foreach ($value as $ele) {
                        ScreenQuestionResponse::create([
                            'value' => $ele,
                            'model_id' => $this->id,
                            'question_id' => $screenQuestion->id,
                            'model_type' => $this->getMorphClass(),
                        ]);
                    }
                }
            } else {
                ScreenQuestionResponse::updateOrCreate([
                    'model_id' => $this->id,
                    'question_id' => $screenQuestion->id,
                    'model_type' => $this->getMorphClass(),
                ], ['value' => $value,]);
            }
        }
    }
}
