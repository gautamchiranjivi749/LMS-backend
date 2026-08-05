<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
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
        'course_id'=>'sometimes|exists:courses,id',

        'title'=>'sometimes|string|max:255',

        'description'=>'nullable|string',

         'video_type' => [
                'sometimes',
                'in:upload,youtube,vimeo'
            ],

            'video_url' => [
                'nullable',
                'url'
            ],

            'video_file' => [
                'nullable',
                'file',
                'mimes:mp4,mov,avi,mkv',
                'max:51200'
            ],

        'duration'=>'nullable|integer|min:1',

        'lesson_order'=>'nullable|integer|min:1',

        'is_preview'=>'nullable|boolean',

        'status'=>'nullable|boolean',
        ];
    }
}
