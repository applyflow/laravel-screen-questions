<?php

namespace Applyflow\LaravelScreenQuestions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ScreenQuestionResponse extends Model
{
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
        'value',
    ];

    protected $casts = [
        'value_int' => 'integer',
    ];

    protected $appends = ['value'];

    /**
     * @var string[]
     */
    const VALUE_FIELDS = [
        ScreenQuestion::TYPE_TEXT => 'value_str',
        ScreenQuestion::TYPE_RADIO => 'value_str',
        ScreenQuestion::TYPE_SELECT => 'value_str',
        ScreenQuestion::TYPE_MULTISELECT => 'value_str',
        ScreenQuestion::TYPE_NUMBER => 'value_int',
        ScreenQuestion::TYPE_CHECKBOX => 'value_int',
        ScreenQuestion::TYPE_TEXTAREA => 'value_text',
        ScreenQuestion::TYPE_PHONE => 'value_str',
        ScreenQuestion::TYPE_EMAIL => 'value_str',
        ScreenQuestion::TYPE_PASSWORD => 'value_str',
        ScreenQuestion::TYPE_FILE => 'value_str',
        ScreenQuestion::TYPE_IMAGE => 'value_str',
        ScreenQuestion::TYPE_URL => 'value_str',
        ScreenQuestion::TYPE_DATE => 'value_str',
        ScreenQuestion::TYPE_DATETIME => 'value_str',
        ScreenQuestion::TYPE_MONTH => 'value_str',
        ScreenQuestion::TYPE_WEEK => 'value_str',
        ScreenQuestion::TYPE_TIME => 'value_str',
    ];

    /**
     * ScreenQuestionResponse constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        /*
         * We have to do this because the `value` mutator
         * depends on `question_id` being set. If `value`
         * is declared earlier than `question_id` in a
         * create() array, the mutator will fail.
         */

        $this->attributes = $attributes;

        $this->bootIfNotBooted();
        $this->initializeTraits();
        $this->syncOriginal();
        $this->fill($attributes);

        $this->table = config('screen-questions.tables.field-responses', 'custom_field_responses');
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
     * Get the questionable belonging to the model.
     *
     */
    public function questionable()
    {
        return $this->morphTo();
    }

    /**
     * Add a scope to return models that match the given value.
     *
     * @param $query
     * @param $value
     * @return mixed
     */
    public function scopeHasValue($query, $value)
    {
        return $query
            ->where('value_str', $value)
            ->orWhere('value_int', $value)
            ->orWhere('value_text', $value);
    }

    /**
     * @param $value
     * @return bool|mixed
     */
    public function formatValue($value)
    {
        // Checkboxes send a default value of "on", so we need to booleanize the value
        if ($this->questionable->type === 'checkbox') {
            $value = !!$value;
        }

        if ($this->questionable->type === 'number') {
            $value = (int) $value;
        }

        return $value;
    }

    /**
     * @return bool|mixed
     */
    public function getValueAttribute()
    {
        return $this->formatValue(
            $this->attributes[$this->valueField()]
        );
    }

    /**
     * @return mixed|string
     */
    public function getValueFriendlyAttribute()
    {
        if ($this->questionable->type === 'checkbox') {
            return $this->value ? 'Checked' : 'Unchecked';
        }

        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value_int'] = null;
        $this->attributes['value_str'] = null;
        $this->attributes['value_text'] = null;
        unset($this->attributes['value']);

        $this->attributes[$this->valueField()] = $this->formatValue($value);
    }

    /**
     * @return string
     */
    protected function valueField()
    {
        return self::VALUE_FIELDS[$this->questionable->type];
    }

    public static function formatScreenQuestionResponse(ResourceCollection $responses)
    {
        return $responses->groupBy('question_id')->map(function ($item, $key) {
            $first_item = $item[0];
            $values = $item->pluck('value');

            if ($values) {
                if ($first_item->questionable->type == ScreenQuestion::TYPE_MULTISELECT) {
                    $first_item->value = $values;
                } else {
                    $first_item->value = $values[0];
                }
            } else {
                $first_item->value = null;
            }

            return $first_item;
        })->values();
    }
}
