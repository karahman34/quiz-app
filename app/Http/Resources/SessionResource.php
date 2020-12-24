<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            'code' => $this->code,
            'available_for' => $this->available_for,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'packet' => [
                'id' => $this->packet->id,
                'title' => $this->packet->title,
                'created_at' => $this->packet->created_at,
                'updated_at' => $this->packet->updated_at,
                'quizzes' => new QuizzesCollection($this->packet->quizzes),
            ]
        ];
    }
}
