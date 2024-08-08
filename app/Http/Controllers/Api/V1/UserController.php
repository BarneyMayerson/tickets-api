<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;
    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(
            User::query()->has("tickets")->filter($filters)->paginate(),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $this->authorize("store", User::class);

            return new UserResource(User::create($request->mappedAttributes()));
        } catch (AuthorizationException $e) {
            return $this->error(
                "You are not authorized to create that resource",
                Response::HTTP_FORBIDDEN,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($this->include("tickets")) {
            return new UserResource($user->load("tickets"));
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $user_id)
    {
        try {
            $user = User::query()->findOrFail($user_id);

            $this->authorize("update", $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);
        } catch (ModelNotFoundException $exception) {
            return $this->error("User cannot be found", Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return $this->error(
                "You are not authorized to update that resource",
                Response::HTTP_FORBIDDEN,
            );
        }
    }

    /**
     * Replace the specified resource in storage. (PUT request method)
     */
    public function replace(ReplaceUserRequest $request, $user_id)
    {
        try {
            $user = User::query()->findOrFail($user_id);

            $this->authorize("replace", $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);
        } catch (ModelNotFoundException $exception) {
            return $this->error("User cannot be found", Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        try {
            $user = User::query()->findOrFail($user_id);

            $this->authorize("delete", $user);

            $user->delete();

            return $this->ok("User successfully deleted.");
        } catch (ModelNotFoundException $exception) {
            return $this->error("User cannot be found", Response::HTTP_NOT_FOUND);
        }
    }
}
