<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AuthorTicketsController extends ApiController
{
    public function index($authorId, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where("user_id", $authorId)->filter($filters)->paginate(),
        );
    }

    public function store($author_id, StoreTicketRequest $request)
    {
        $model = [
            "title" => $request->input("data.attributes.title"),
            "description" => $request->input("data.attributes.description"),
            "status" => $request->input("data.attributes.status"),
            "user_id" => $author_id,
        ];

        return new TicketResource(Ticket::create($model));
    }

    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::query()->findOrFail($ticket_id);

            if ($ticket->user_id == $author_id) {
                $attributes = [
                    "title" => $request->input("data.attributes.title"),
                    "description" => $request->input("data.attributes.description"),
                    "status" => $request->input("data.attributes.status"),
                    "user_id" => $author_id,
                ];

                $ticket->update($attributes);

                return new TicketResource($ticket);
            } else {
                // TODO:
            }
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket cannot be found", Response::HTTP_NOT_FOUND);
        }
    }

    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::query()->findOrFail($ticket_id);

            if ($ticket->user_id == $author_id) {
                $ticket->delete();

                return $this->ok("Ticket successfully deleted.");
            }

            return $this->error("Ticket cannot found (user cause)", Response::HTTP_NOT_FOUND);
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket cannot found", Response::HTTP_NOT_FOUND);
        }
    }
}
