<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Status;
use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $authorIdAttr = $this->routeIs("v1.tickets.store")
            ? "data.relationships.author.data.id"
            : "author";

        $user = Auth::user();
        $authorRule = "required|integer|exists:users,id";

        $rules = [
            "data" => "required|array",
            "data.attributes" => "required|array",
            "data.attributes.title" => "required|string",
            "data.attributes.description" => "required|string",
            "data.attributes.status" => "required|string|in:" . Status::valuesToString(),
            $authorIdAttr => $authorRule . "|size:" . $user->id,
        ];

        if ($user->tokenCan(Abilities::CreateTicket)) {
            $rules[$authorIdAttr] = $authorRule;
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->routeIs("v1.authors.tickets.store")) {
            $this->merge([
                "author" => $this->route("author"),
            ]);
        }
    }
}
