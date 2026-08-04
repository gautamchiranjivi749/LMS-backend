<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
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
            
              'course_id' => 'required|exists:courses,id',

        'title' => 'required|string|max:255',

        'description' => 'nullable|string',

        'video_type' => 'required|in:upload,youtube,vimeo',

        'video_url' => 'nullable|url',

        'video_file' => 'nullable|file|mimes:mp4,mov,avi|max:51200',

        'duration' => 'nullable|integer|min:1',

        'order' => 'required|integer|min:1',

        'is_preview' => 'boolean',

        'status' => 'boolean',
        ];
    }
}
