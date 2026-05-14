<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:100'],
            'guest_phone' => ['nullable', 'string', 'max:20'],
            'attendance' => ['required', 'in:hadir,tidak_hadir,mungkin'],
            'pax' => ['integer', 'min:1', 'max:10'],
            'message' => ['nullable', 'string', 'max:500'],
        ];
    }
}
