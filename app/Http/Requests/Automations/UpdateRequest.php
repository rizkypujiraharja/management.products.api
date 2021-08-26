<?php

namespace App\Http\Requests\Automations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the condition rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $config = collect(config('automations.when'));
        $availableEvent = $config->pluck('class');
        $availableValidation = $config->where('class', $this->event_class)
            ->pluck('conditions')
            ->collapse()->pluck('class')
            ->unique();
        $availableExecution = $config->where('class', $this->event_class)
            ->pluck('actions')
            ->collapse()->pluck('class')
            ->unique();
        return [
            'name' => 'required|min:3|max:200',
            'event_class' => [
                'required',
                Rule::in($availableEvent),
            ],
            'enabled' => 'required|boolean',
            'priority' => 'required|numeric',

            // Conditions
            'conditions.*.condition_class' => [
                'required',
                Rule::in($availableValidation),
            ],
            'conditions.*.condition_value' => 'required|string',

            // Executions
            'actions.*.action_class' => [
                'required',
                Rule::in($availableExecution),
            ],
            'actions.*.action_value' => 'required|string'
        ];
    }
}