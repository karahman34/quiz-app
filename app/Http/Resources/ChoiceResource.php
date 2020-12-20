<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'text' => $this->text,
            'is_right' => $this->is_right,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => [
                'url' => $this->getImageUrl(),
                'original' => is_null($this->image) ? null : $this->image->path,
            ],
        ];
    }
}
