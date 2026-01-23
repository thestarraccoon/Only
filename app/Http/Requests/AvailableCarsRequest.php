<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailableCarsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_at' => ['required', 'date', 'after_or_equal:now'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'car_model_id' => ['nullable', 'integer', 'exists:car_models,id'],
            'comfort_category_id' => ['nullable', 'integer', 'exists:comfort_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_at.required' => __('validation.start_at.required'),
            'start_at.after_or_equal' => __('start_at.after_or_equal'),
            'end_at.required' => __('validation.end_at.required'),
            'end_at.after' => __('end_at.after'),
            'car_model_id.exists' => __('car_model_id.exists'),
            'comfort_category_id.exists' => __('comfort_category_id.exists'),
        ];
    }
}
