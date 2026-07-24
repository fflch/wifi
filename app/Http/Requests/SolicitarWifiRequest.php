<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitarWifiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'document' => ['required', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'reason' => ['required', 'string', 'max:500'],
            'client_mac' => ['required', 'string', 'max:17', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}[0-9A-Fa-f]{2}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'document.required' => 'O documento é obrigatório.',
            'reason.required' => 'O motivo da solicitação é obrigatório.',
            'reason.max' => 'O motivo deve ter no máximo 500 caracteres.',
            'client_mac.required' => 'O endereço MAC do dispositivo é obrigatório.',
            'client_mac.regex' => 'O endereço MAC deve estar no formato XX:XX:XX:XX:XX:XX ou XX-XX-XX-XX-XX-XX.',
        ];
    }
}
