<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PacketResource extends JsonResource
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
            'title' => $this->title,
            'lasts_for' => $this->lasts_for,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author' => $this->author,
            'quizzes_count' => $this->quizzes_count,
            'quizzes' => new QuizzesCollection($this->quizzes),
        ];
    }
}
