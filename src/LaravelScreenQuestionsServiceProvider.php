<?php

namespace Applyflow\LaravelScreenQuestions;

use Illuminate\Support\ServiceProvider;

class LaravelScreenQuestionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/screen-questions.php' => config_path('screen-questions.php'),
        ]);

        if (!class_exists('CreateScreenQuestionsTables')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_screen_questions_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_screen_questions_tables.php'),
            ], 'migrations');
        }
    }
}
