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

            'video_url' => [
            'required_if:video_type,youtube,vimeo',
            'nullable',
            'url',
        ],

        'video_file' => [
            'required_if:video_type,upload',
            'nullable',
            'file',
            'mimes:mp4,mov,avi,mkv',
            'max:51200',
        ],

        'duration' => 'nullable|integer|min:1',

        'lesson_order' => 'required|integer|min:1',

        'is_preview' => 'boolean',

        'status' => 'boolean',
        ];
    }
    //custom messages for validation errors
     public function messages(): array
    {
        return [

            'course_id.required' => 'Course is required.',

            'course_id.exists' => 'Selected course does not exist.',

            'title.required' => 'Lesson title is required.',

            'video_type.required' => 'Video type is required.',

            'video_type.in' => 'Video type must be upload, youtube or vimeo.',

            'video_url.url' => 'Please enter a valid video URL.',

            'video_file.mimes' => 'Only MP4, MOV, AVI and MKV videos are allowed.',

            'video_file.max' => 'Maximum video size is 50MB.',

            'order.required' => 'Lesson order is required.',

        ];
    }
}
