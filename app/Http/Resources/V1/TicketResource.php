<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    // public static $wrap = "ticket";

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "ticket",
            "id" => $this->id,
            "attributes" => [
                "title" => $this->title,
                "description" => $this->description,
                "status" => $this->status,
                "createdAt" => $this->created_at,
                "updatedAt" => $this->updated_at,
            ],
            "relationships" => [
                "author" => [
                    "data" => [
                        "type" => "user",
                        "id" => $this->user_id,
                    ],
                    "links" => [
                        "self" => "TODO",
                    ],
                ],
            ],
            "links" => [
                "self" => route("v1.tickets.show", ["ticket" => $this->id]),
            ],
        ];
    }
}
