<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
