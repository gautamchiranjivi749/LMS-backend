<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
            'title' => 'sometimes|required|max:255',
            'description' => 'sometimes|required',
            'price' => 'sometimes|required|numeric',
            'level' => 'sometimes|required|in:beginner,intermediate,advanced',
            'language' => 'sometimes|required|string',
            'category_id' => 'required|exists:categories,id',

            'skill_ids' => 'nullable|array',

            'skill_ids.*' => 'exists:skills,id',

            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'sometimes|boolean',
        ];
    }
}
