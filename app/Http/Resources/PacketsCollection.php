<?php

namespace App\Http\Resources;

use App\Models\Packet;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PacketsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function (Packet $packet) {
            return [
                'id' => $packet->id,
                'title' => $packet->title,
                'created_at' => $packet->created_at,
                'updated_at' => $packet->updated_at,
                'quizzes_count' => $packet->quizzes_count,
            ];
        });
    }
}
