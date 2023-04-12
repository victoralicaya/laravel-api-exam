<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileUploadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'created_at' => $this->created_at
        ];
    }

    public function toResponse($request)
    {
        return parent::toResponse($request)->setStatusCode(200);
    }
}
