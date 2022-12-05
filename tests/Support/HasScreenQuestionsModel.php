<?php

namespace Applyflow\Tests\Support;

use Applyflow\LaravelScreenQuestions\Traits\HasScreenQuestions;
use Illuminate\Database\Eloquent\Model;

class HasScreenQuestionsModel extends Model
{
    use HasScreenQuestions;
}
