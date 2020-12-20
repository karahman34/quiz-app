<?php

namespace App\Http\Resources;

use App\Models\Choice;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChoicesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function (Choice $choice) {
            return new ChoiceResource($choice);
        });
    }
}
