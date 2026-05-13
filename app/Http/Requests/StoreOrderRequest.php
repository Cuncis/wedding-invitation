<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invitation_id' => ['required', 'integer', 'exists:invitations,id'],
            'subtotal' => ['required', 'integer', 'min:0'],
            'discount' => ['nullable', 'integer', 'min:0'],
            'total_amount' => ['required', 'integer', 'min:0'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
