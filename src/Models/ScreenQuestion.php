<?php

namespace Applyflow\LaravelScreenQuestions\Models;

use Carbon\Carbon;
use Applyflow\LaravelScreenQuestions\Traits\Archives;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ScreenQuestion extends Model
{
    use HasFactory, Archives;

    /**
     * @var string
     */
    const TYPE_CHECKBOX = 'checkbox';

    /**
     * @var string
     */
    const TYPE_NUMBER = 'number';

    /**
     * @var string
     */
    const TYPE_RADIO = 'radio';

    /**
     * @var string
     */
    const TYPE_SELECT = 'select';

    /**
     * @var string
     */
    const TYPE_MULTISELECT = 'multiselect';

    /**
     * @var string
     */
    const TYPE_TEXT = 'text';

    /**
     * @var string
     */
    const TYPE_TEXTAREA = 'textarea';

    /**
     * @var string
     */
    const TYPE_EMAIL = 'email';

    const TYPE_PASSWORD = 'password';

    /**
     * @var string
     */
    const TYPE_PHONE = 'phone';

    const TYPE_FILE = 'file';

    const TYPE_IMAGE = 'image';

    const TYPE_URL = 'url';

    const TYPE_DATE = 'date';

    const TYPE_DATETIME = 'datetime';

    const TYPE_MONTH = 'month';

    const TYPE_WEEK = 'week';

    const TYPE_TIME = 'time';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'type',
        'title',
        'slug',
        'description',
        'options',
        'rules',
        'group',
        'required',
        'default_value',
        'misc',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
        'rules' => 'json',
        'archived_at' => 'datetime',
        'required' => 'boolean',
        'misc' => 'json',
    ];

    /**
     * ScreenQuestion constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('screen-questions.tables.fields', 'custom_fields');
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the morphable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the responses belonging to the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function responses()
    {
        return $this->morphMany(ScreenQuestionResponse::class, 'questionable');
    }

    /**
     * Archive the model.
     *
     * @return $this
     */
    public function archive()
    {
        $this->forceFill([
            'archived_at' => now(),
        ])->save();

        return $this;
    }

    /**
     * Unarchive the model.
     *
     * @return $this
     */
    public function unarchive()
    {
        $this->forceFill([
            'archived_at' => null,
        ])->save();

        return $this;
    }

    /**
     * Get the validation rules attribute.
     *
     * @return mixed
     */
    public function getValidationRulesAttribute()
    {
        $typeRules = $this->getFieldValidationRules($this->required)[$this->type];

        array_unshift($typeRules, $this->required ? 'required' : 'nullable');

        // if ($this->rules) {
        //     $rules = [];
        //     foreach ($this->rules as $key => $value) {
        //         $rules[] = $key . ':' . $value;
        //     }
        //     $typeRules = array_merge($typeRules, $rules);
        // }

        return $typeRules;
    }

    public function getValuesAttribute()
    {
        $responses = $this->responses;
        $values = [];
        foreach ($responses as $ele) {
            $ele->field = $this;
            $values[] = $ele->value;
        }

        if ($values) {
            if ($this->type == self::TYPE_MULTISELECT) {
                $result = $values;
            } else {
                $result = $values[0];
            }
        } else {
            $result = null;
        }

        return $result;
    }


    /**
     * Get the field validation rules.
     *
     * @param $required
     * @return array
     */
    protected function getFieldValidationRules($required)
    {
        return [
            self::TYPE_CHECKBOX => [
                'boolean'
            ],

            self::TYPE_NUMBER => [
                'integer',
            ],

            self::TYPE_SELECT => [
                'string',
                Rule::in($this->options),
            ],

            self::TYPE_MULTISELECT => [
                'array',
                Rule::in($this->options),
            ],

            self::TYPE_RADIO => [
                'string',
                Rule::in($this->options),
            ],

            self::TYPE_TEXT => [
                'string',
            ],

            self::TYPE_TEXTAREA => [
                'string',
            ],

            self::TYPE_EMAIL => [
                'email'
            ],

            self::TYPE_PHONE => [
                'string'
            ],

            self::TYPE_PASSWORD => [
                'string'
            ],

            self::TYPE_FILE => [
                'uuid'
            ],

            self::TYPE_IMAGE => [
                'uuid'
            ],

            self::TYPE_URL => [
                'string'
            ],

            self::TYPE_DATE => [
                'date'
            ],
        ];
    }
}
