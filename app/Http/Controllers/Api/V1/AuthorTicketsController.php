<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AuthorTicketsController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    public function index($authorId, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where("user_id", $authorId)->filter($filters)->paginate(),
        );
    }

    public function store(StoreTicketRequest $request, $author_id)
    {
        try {
            $this->authorize("store", Ticket::class);

            return new TicketResource(
                Ticket::create(
                    $request->mappedAttributes([
                        "author" => "user_id",
                    ]),
                ),
            );
        } catch (AuthorizationException $e) {
            return $this->error(
                "You are not authorized to create that resource",
                Response::HTTP_FORBIDDEN,
            );
        }

        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    public function update(UpdateTicketRequest $request, $author_id, $ticket_id)
    {
        // PATCH
        try {
            $ticket = Ticket::query()
                ->where("id", $ticket_id)
                ->where("user_id", $author_id)
                ->findOrFail($ticket_id);

            $this->authorize("update", $ticket);
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket cannot be found", Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return $this->error(
                "You are not authorized to update that resource",
                Response::HTTP_FORBIDDEN,
            );
        }
    }

    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::query()
                ->where("id", $ticket_id)
                ->where("user_id", $author_id)
                ->findOrFail($ticket_id);

            $this->authorize("replace", $ticket);
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket cannot be found", Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return $this->error(
                "You are not authorized to replace that resource",
                Response::HTTP_FORBIDDEN,
            );
        }
    }

    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::query()
                ->where("id", $ticket_id)
                ->where("user_id", $author_id)
                ->findOrFail($ticket_id);

            $this->authorize("delete", $ticket);
            $ticket->delete();

            return $this->ok("Ticket successfully deleted.");
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket cannot found", Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return $this->error(
                "You are not authorized to delete that resource",
                Response::HTTP_FORBIDDEN,
            );
        }
    }
}
