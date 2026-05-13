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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'attendance_status' => ['required', 'in:attending,not_attending,maybe'],
            'guest_count' => ['nullable', 'integer', 'min:0', 'max:10'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
