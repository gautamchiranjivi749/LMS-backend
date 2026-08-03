<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'certificate_no' => $this->certificate_no,

            'student' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'course' => [
                'id' => $this->course->id,
                'title' => $this->course->title,
            ],

            'quiz_attempt' => [
                'id' => $this->quizAttempt?->id,
                'score' => $this->quizAttempt?->score,
                'percentage' => $this->quizAttempt?->percentage,
            ],

            'issued_date' => $this->issued_date,

            'pdf' => $this->pdf_path
                ? asset('storage/'.$this->pdf_path)
                : null,

            'created_at' => $this->created_at,
        ];
    }
}
