<?php

namespace Applyflow\LaravelScreenQuestions\Interfaces;

interface ScreenQuestionable
{
    /**
     * Get the custom fields belonging to this model.
     *
     * @return mixed
     */
    public function screenQuestions();
}
