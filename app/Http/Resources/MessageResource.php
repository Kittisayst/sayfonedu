<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender' => new UserResource($this->sender),
            'receiver' => new UserResource($this->receiver),
            'subject' => $this->subject,
            'content' => $this->message_content,
            'attachment' => $this->attachment,
            'read_status' => $this->read_status,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
} 