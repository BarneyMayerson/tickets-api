<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Aoi\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class TicketController extends ApiController
{
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
        try {
            User::findOrFail($request->input("data.relationships.author.data.id"));
        } catch (ModelNotFoundException $exception) {
            return $this->ok("User not found.", [
                "error" => "The provided user id does not exists.",
            ]);
        }

        $model = [
            "title" => $request->input("data.attributes.title"),
            "description" => $request->input("data.attributes.description"),
            "status" => $request->input("data.attributes.status"),
            "user_id" => $request->input("data.relationships.author.data.id"),
        ];

        return new TicketResource(Ticket::create($model));
    }

    /**
     * Display the specified resource.
     */
    public function show($ticket_id)
    {
        try {
            $ticket = Ticket::query()->findOrFail($ticket_id);

            if ($this->include("author")) {
                return new TicketResource($ticket->load("author"));
            }

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket cannot be found", Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage. (PATCH request method)
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {
        //
    }

    /**
     * Replace the specified resource in storage. (PUT request method)
     */
    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::query()->findOrFail($ticket_id);

            $attributes = [
                "title" => $request->input("data.attributes.title"),
                "description" => $request->input("data.attributes.description"),
                "status" => $request->input("data.attributes.status"),
                "user_id" => $request->input("data.relationships.author.data.id"),
            ];

            $ticket->update($attributes);

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket cannot be found", Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::query()->findOrFail($ticket_id);
            $ticket->delete();

            return $this->ok("Ticket successfully deleted.");
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket cannot found", Response::HTTP_NOT_FOUND);
        }
    }
}
