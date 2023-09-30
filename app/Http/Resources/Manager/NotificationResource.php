<?php

namespace App\Http\Resources\Manager;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'is_read' => $this->is_read,
            'link' => $this->link,
            'created_at' => $this->created_at,
            'permissions' => [
                'delete' => true,
                'view' => !empty($this->link)
            ],
        ];
    }
}
