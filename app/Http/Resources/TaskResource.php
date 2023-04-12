<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $prio = ["LOW", "NORMAL", "HIGH", "URGENT"];

        if (!is_null($this->priority)) {
            foreach ($prio as $key => $value) {
                if ($key + 1 == $this->priority) {
                    $text = $value;
                }
            }
        }

        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'title' => $this->title,
            'description' => $this->description,
            'tags' => TagResource::collection($this->tags),
            'files' => FileUploadResource::collection($this->files),
            'due_date' => $this->due_date,
            'priority' => [
                'priority_num' => $this->priority,
                'text' => $text
            ],
            'status' => $this->status,
            'date_completed' => !is_null($this->date_completed) ? Carbon::parse($this->date_completed)->format('Y-m-d H:i:s') : null,
            'archive_date' => !is_null($this->archive) ? Carbon::parse($this->archive)->format('Y-m-d H:i:s') : '',
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ];
    }

    public function toResponse($request)
    {
        return parent::toResponse($request)->setStatusCode(200);
    }
}
