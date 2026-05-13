<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvitationConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'music_url' => ['nullable', 'url', 'max:2048'],
            'font_family' => ['nullable', 'string', 'max:120'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'custom_css' => ['nullable', 'string'],
        ];
    }
}
