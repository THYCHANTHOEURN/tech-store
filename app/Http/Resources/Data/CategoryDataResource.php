<?php

namespace App\Http\Resources\Data;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            // 'children'          => CategoryChildrenDataResource::collection($this->children),
            'image_url'         => $this->image_url,
            // 'products_count'    => $this->products_count,
            // 'color'             => $this->color ?? $this->color(),
        ];
    }
}
