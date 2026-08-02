<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
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
        return [
             'quiz_id'=>'required|exists:quizzes,id',

        'question'=>'required|string',

        'question_type'=>[
            'required',
            'in:mcq,true_false'
        ],

        'marks'=>'required|integer|min:1',

        'question_order'=>'nullable|integer|min:1',

        'status'=>'nullable|boolean',
        ];
    }
}
