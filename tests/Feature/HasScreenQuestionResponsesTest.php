<?php

namespace Applyflow\Tests\Feature;

use Applyflow\LaravelScreenQuestions\Models\ScreenQuestion;
use Applyflow\LaravelScreenQuestions\Models\ScreenQuestionResponse;
use Applyflow\Tests\Support\Survey;
use Applyflow\Tests\Support\SurveyResponse;
use Applyflow\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasScreenQuestionResponsesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function custom_fields_responses_can_be_created_and_accessed_on_models_with_trait()
    {
        $screenQuestionModel = Survey::create();
        $screenQuestionResponseModel = SurveyResponse::create();

        $screenQuestion = ScreenQuestion::factory()->make([
            'model_id' => $screenQuestionModel->id,
            'model_type' => get_class($screenQuestionModel),
        ]);

        $screenQuestionModel->screenQuestions()->save($screenQuestion);

        $screenQuestionResponse = ScreenQuestionResponse::make([
            'model_id' => $screenQuestionResponseModel->id,
            'model_type' => get_class($screenQuestionResponseModel),
            'question_id' => $screenQuestion->fresh()->id,
            'value_str' => 'Best Rapper Alive',
        ]);

        $screenQuestionResponseModel->screenQuestionResponses()->save($screenQuestionResponse);

        $this->assertCount(1, $screenQuestionResponseModel->fresh()->screenQuestionResponses);
        $this->assertEquals('Best Rapper Alive', $screenQuestionResponseModel->fresh()->screenQuestionResponses->first()->value_str);
    }

    /** @test */
    public function whereField_method_allows_filtering_responses()
    {
        $screenQuestionModel = Survey::create();
        $firstResponseModel = SurveyResponse::create();
        $secondResponseModel = SurveyResponse::create();

        $firstField = ScreenQuestion::factory()->create([
            'model_id' => $screenQuestionModel->id,
            'model_type' => get_class($screenQuestionModel),
        ]);

        $firstResponse = ScreenQuestionResponse::create([
            'model_id' => $firstResponseModel->id,
            'model_type' => get_class($firstResponseModel),
            'question_id' => $firstField->id,
            'value_str' => 'Hit Em Up',
        ]);

        $secondResponse = ScreenQuestionResponse::create([
            'model_id' => $secondResponseModel->id,
            'model_type' => get_class($secondResponseModel),
            'question_id' => $firstField->id,
            'value_str' => 'Best Rapper Alive',
        ]);

        $firstResponseModel->screenQuestionResponses()->save($firstResponse);
        $secondResponseModel->screenQuestionResponses()->save($secondResponse);

        $this->assertCount(1, SurveyResponse::whereField($firstField, 'Hit Em Up')->get());
        $this->assertEquals($firstResponse->id, SurveyResponse::whereField($firstField, 'Hit Em Up')->first()->id);

        $this->assertCount(1, SurveyResponse::whereField($firstField, 'Best Rapper Alive')->get());
        $this->assertEquals($secondResponse->id, SurveyResponse::whereField($firstField, 'Best Rapper Alive')->first()->id);
    }


    /** @test */
    public function value_getter_and_setter_work_fine()
    {
        $screenQuestionModel = Survey::create();
        $screenQuestionResponseModel = SurveyResponse::create();

        $screenQuestion = ScreenQuestion::factory()->make([
            'model_id' => $screenQuestionModel->id,
            'model_type' => get_class($screenQuestionModel),
            'type' => 'text',
        ]);

        $screenQuestionModel->screenQuestions()->save($screenQuestion);

        $screenQuestionResponse = ScreenQuestionResponse::make([
            'model_id' => $screenQuestionResponseModel->id,
            'model_type' => get_class($screenQuestionResponseModel),
            'question_id' => $screenQuestion->fresh()->id,
            'value_str' => 'Best Rapper Alive',
        ]);

        $screenQuestionResponseModel->screenQuestionResponses()->save($screenQuestionResponse);
        $this->assertEquals('Best Rapper Alive', $screenQuestionResponse->fresh()->value);
        $screenQuestionResponse->update(['value' => 'Hit Em Up']);
        $this->assertEquals('Hit Em Up', $screenQuestionResponse->fresh()->value);
    }
}
