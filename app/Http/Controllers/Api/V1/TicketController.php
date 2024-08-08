<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class TicketController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        if ($this->authorize("store", Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttributes()));
        }

        return $this->error(
            "You are not authorized to create that resource",
            Response::HTTP_FORBIDDEN,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        if ($this->include("author")) {
            return new TicketResource($ticket->load("author"));
        }

        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage. (PATCH request method)
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        if ($this->authorize("update", $ticket)) {
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        }
    }

    /**
     * Replace the specified resource in storage. (PUT request method)
     */
    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        if ($this->authorize("replace", $ticket)) {
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        if ($this->authorize("delete", $ticket)) {
            $ticket->delete();

            return $this->ok("Ticket successfully deleted.");
        }
    }
}
