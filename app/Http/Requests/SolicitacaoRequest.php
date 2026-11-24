<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitacaoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nome' => 'required|max:255',
            'email' => 'required|email|max:255',
            'documento' => 'required|max:20',
            'telefone' => 'nullable|max:20',
            'motivo' => 'required|min:10|max:500',
            'mac' => 'required|mac_address',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'documento.required' => 'O CPF ou passaporte é obrigatório.',
            'motivo.required' => 'O motivo da visita é obrigatório.',
            'motivo.min' => 'O motivo deve ter pelo menos 10 caracteres.',
            'motivo.max' => 'O motivo deve ter no máximo 500 caracteres.',
            'mac.required' => 'O endereço MAC do dispositivo é obrigatório.',
            'mac.mac_address' => 'O endereço MAC do dispositivo é inválido.',
        ];
    }
}
