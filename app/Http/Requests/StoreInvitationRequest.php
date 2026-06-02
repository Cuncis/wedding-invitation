<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'theme_id' => ['nullable', 'integer', 'exists:themes,id'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('invitations', 'slug')->ignore($this->route('invitation'))],
            'groom_name' => ['required', 'string', 'max:255'],
            'bride_name' => ['required', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
        ];
    }
}
