<?php

namespace Applyflow\Tests\Support;

use Applyflow\LaravelScreenQuestions\Traits\HasScreenQuestions;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasScreenQuestions;
}
