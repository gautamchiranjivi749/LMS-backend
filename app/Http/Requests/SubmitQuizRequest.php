<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [  'attempt_id' => [
                'required',
                'integer',
                'exists:quiz_attempts,id',
            ],

            'answers' => [
                'required',
                'array',
                'min:1',
            ],

            'answers.*.question_id' => [
                'required',
                'integer',
                'exists:questions,id',
            ],

            'answers.*.question_option_id' => [
                'nullable',
                'integer',
                'exists:question_options,id',
            ],

            'answers.*.answer_text' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'attempt_id.required' => 'Quiz attempt is required.',
            'answers.required' => 'Please submit at least one answer.',
            'answers.array' => 'Answers must be an array.',
            'answers.*.question_id.required' => 'Question ID is required.',
            'answers.*.question_option_id.exists' => 'Selected option is invalid.',
        ];
    }
}
