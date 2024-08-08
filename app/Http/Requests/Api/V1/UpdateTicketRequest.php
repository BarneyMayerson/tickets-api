<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Status;
use App\Permissions\V1\Abilities;

class UpdateTicketRequest extends BaseTicketRequest
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
        $rules = [
            "data.attributes.title" => "sometimes|string",
            "data.attributes.description" => "sometimes|string",
            "data.attributes.status" => "sometimes|string|in:" . Status::valuesToString(),
            "data.relationships.author.data.id" => "prohibited",
        ];

        if ($this->user()->tokenCan(Abilities::UpdateTicket)) {
            $rules["data.relationships.author.data.id"] = "sometimes|integer";
        }

        return $rules;
    }
}
