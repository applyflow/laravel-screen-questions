<?php

namespace Applyflow\Tests\Support;

use Applyflow\LaravelScreenQuestions\Traits\HasScreenQuestionResponses;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasScreenQuestionResponses;
}
