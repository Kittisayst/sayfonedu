<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'request_type' => $this->request_type,
            'subject' => $this->subject,
            'content' => $this->content,
            'attachment' => $this->attachment,
            'status' => $this->status,
            'response' => $this->response,
            'handler' => $this->handler ? new UserResource($this->handler) : null,
            'handled_at' => $this->handled_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 