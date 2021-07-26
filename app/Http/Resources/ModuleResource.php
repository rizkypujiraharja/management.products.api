<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'service_provider_class'    => $this->service_provider_class,
            'enabled'                   => $this->enabled,
            'name'                      => $this->name,
            'description'               => $this->description,
        ];
    }
}
