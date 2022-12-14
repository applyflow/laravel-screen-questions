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
        foreach ($fields as $field) {
            $screenQuestion = $field["question"];

            if (!$screenQuestion) {
                continue;
            }

            $job_uuid = $field["job_uuid"];

            if (!$job_uuid) {
                continue;
            }

            $value = $field["value"];
            $order = $field["order"] ?? null;

            if ($screenQuestion->type == ScreenQuestion::TYPE_MULTISELECT) {
                ScreenQuestionResponse::where(
                    [
                        'model_id' => $this->id,
                        'questionable_id' => $screenQuestion->id,
                        'questionable_type' => $screenQuestion->getMorphClass(),
                        'job_uuid' => $job_uuid,
                        'model_type' => $this->getMorphClass(),
                    ]
                )->delete();

                if (is_array($value)) {
                    foreach ($value as $ele) {
                        ScreenQuestionResponse::create([
                            'value' => $ele,
                            'model_id' => $this->id,
                            'questionable_id' => $screenQuestion->id,
                            'questionable_type' => $screenQuestion->getMorphClass(),
                            'job_uuid' => $job_uuid,
                            'model_type' => $this->getMorphClass(),
                            'order' => $order,
                        ]);
                    }
                }
            } else {
                ScreenQuestionResponse::updateOrCreate([
                    'model_id' => $this->id,
                    'questionable_id' => $screenQuestion->id,
                    'questionable_type' => $screenQuestion->getMorphClass(),
                    'job_uuid' => $job_uuid,
                    'model_type' => $this->getMorphClass(),
                ], [
                    'value' => $value,
                    'order' => $order
                ]);
            }
        }
    }
}
