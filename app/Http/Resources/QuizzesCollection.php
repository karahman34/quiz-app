<?php

namespace App\Http\Resources;

use App\Models\Quiz;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuizzesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function (Quiz $quiz) {
            return new QuizResource($quiz);
        });
    }
}
