<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
            'title'=>'required|max:255',

            'category_id' => [
                'required','exists:categories,id',
                ],

            'description'=>'required',

            'price'=>'required|numeric',

            'level'=>'required|in:beginner,intermediate,advanced',

            'language'=>'required'
        ];
    }
}
