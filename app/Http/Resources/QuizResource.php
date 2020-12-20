<?php

namespace App\Http\Resources;

use App\Models\Quiz;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
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
            'packet_id' => $this->packet_id,
            'text' => $this->text,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => [
                'url' => $this->imageUrl(),
                'original' => is_null($this->image) ? null : $this->image->path,
            ],
            'choices_count' => $this->choices_count,
            'choices' => new ChoicesCollection($this->choices),
        ];
    }
}
