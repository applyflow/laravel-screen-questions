<?php

namespace Database\Factories;

use Exception;
use Faker\Provider\Lorem;
use Applyflow\LaravelScreenQuestions\Models\ScreenQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScreenQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScreenQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $typesRequireAnswers = [
            ScreenQuestion::TYPE_TEXT => false,
            ScreenQuestion::TYPE_RADIO => true,
            ScreenQuestion::TYPE_SELECT => true,
            ScreenQuestion::TYPE_NUMBER => false,
            ScreenQuestion::TYPE_CHECKBOX => false,
            ScreenQuestion::TYPE_TEXTAREA => false,
        ];

        $type = array_keys($typesRequireAnswers)[rand(0, count($typesRequireAnswers) - 1)]; // Pick a random type

        return [
            'type' => $type,
            'required' => false,
            'title' => Lorem::sentence(3),
            'description' => Lorem::sentence(3),
            'options' => $typesRequireAnswers ? Lorem::words() : [],
        ];
    }

    /**
     * @return $this
     */
    public function withTypeCheckbox()
    {
        $this->model->type = ScreenQuestion::TYPE_CHECKBOX;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeNumber()
    {
        $this->model->type = ScreenQuestion::TYPE_NUMBER;

        return $this;
    }

    /**
     * @param mixed $answerCount
     * @return $this
     * @throws Exception
     */
    public function withTypeRadio($answerCount = 3)
    {
        $this->model->type = ScreenQuestion::TYPE_RADIO;

        return $this->withAnswers($answerCount);
    }

    /**
     * @param mixed $optionCount
     * @return $this
     * @throws Exception
     */
    public function withTypeSelect($optionCount = 3)
    {
        $this->model->type = ScreenQuestion::TYPE_SELECT;

        return $this->withAnswers($optionCount);
    }

    /**
     * @return $this
     */
    public function withTypeText()
    {
        $this->model->type = ScreenQuestion::TYPE_TEXT;

        return $this;
    }

    /**
     * @return $this
     */
    public function withTypeTextArea()
    {
        $this->model->type = ScreenQuestion::TYPE_TEXTAREA;

        return $this;
    }

    /**
     * @param $defaultValue
     * @return $this
     */
    public function withDefaultValue($defaultValue)
    {
        $this->model->default_value = $defaultValue;

        return $this;
    }

    /**
     * @param mixed $answers
     * @return $this
     * @throws Exception
     */
    public function withAnswers($answers = 3)
    {
        if (is_numeric($answers)) {
            $this->model->options = Lorem::words($answers);

            return $this;
        }

        if (is_array($answers)) {
            $this->model->options = $answers;

            return $this;
        }

        throw new Exception("withAnswers only accepts a number or an array");
    }
}
