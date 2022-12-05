<?php

namespace Applyflow\LaravelScreenQuestions\Exceptions;

use Exception;

class WrongNumberOfFieldsForOrderingException extends Exception
{
    /**
     * WrongNumberOfFieldsForOrderingException constructor.
     *
     * @param $given
     * @param $expected
     */
    public function __construct($given, $expected)
    {
        parent::__construct("Wrong number of fields passed for ordering. {$given} given, {$expected} expected.");
    }
}
