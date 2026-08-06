<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             'id'=>$this->id,

    'user'=>$this->user?->name,

    'action'=>$this->action,

    'module'=>$this->module,

    'module_id'=>$this->module_id,

    'description'=>$this->description,

    'ip_address'=>$this->ip_address,

    'created_at'=>$this->created_at,
        ];
}
}