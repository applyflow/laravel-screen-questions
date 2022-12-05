<?php

namespace Applyflow\Tests\Feature;

use Applyflow\LaravelScreenQuestions\Models\ScreenQuestion;
use Applyflow\Tests\Support\Survey;
use Applyflow\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasScreenQuestionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function custom_fields_can_be_created_and_accessed_on_models_with_trait()
    {
        $model = Survey::create();

        $screenQuestion = ScreenQuestion::factory()->make([
            'model_id' => $model->id,
            'model_type' => get_class($model),
            'description' => 'Lil Wayne',
        ]);

        $model->screenQuestions()->save($screenQuestion);

        $this->assertCount(1, $model->fresh()->screenQuestions);
        $this->assertEquals('Lil Wayne', $model->fresh()->screenQuestions->first()->description);
    }
}
