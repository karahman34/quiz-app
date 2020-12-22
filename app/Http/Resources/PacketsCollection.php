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
            return new PacketResource($packet);
        });
    }
}
