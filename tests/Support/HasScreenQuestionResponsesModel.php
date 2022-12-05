<?php

namespace Applyflow\Tests\Support;

use Applyflow\LaravelScreenQuestions\Traits\HasScreenQuestionResponses;
use Illuminate\Database\Eloquent\Model;

class HasScreenQuestionResponsesModel extends Model
{
    use HasScreenQuestionResponses;
}
