<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MagentoConnectionResource extends JsonResource
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
            'base_url' => $this->base_url,
            'magento_store_id' => $this->magento_store_id,
            'pricing_source_warehouse_id' => $this->pricing_source_warehouse_id,
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'warehouse' => $this->whenLoaded('warehouse', WarehouseResource::make($this->warehouse)),
            'deleted_at' => $this->deleted_at,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'access_token' => $this->access_token_encrypted,
        ];
    }
}
