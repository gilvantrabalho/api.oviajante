<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destino' => [
                'required',
                'string',
                Rule::in([
                    'NACIONAL',
                    'AMERICAS',
                    'EUROPA',
                ]),
            ],

            'data_inicio' => [
                'required',
                'date',
            ],

            'data_fim' => [
                'required',
                'date',
                'after_or_equal:data_inicio',
            ],

            'viajantes' => [
                'required',
                'array',
                'min:1',
            ],

            'viajantes.*.nome' => [
                'required',
                'string',
                'max:255',
            ],

            'viajantes.*.data_nascimento' => [
                'required',
                'date',
                'before:today',
            ],

            'viajantes.*.adicionais' => [
                'nullable',
                'array',
            ],

            'viajantes.*.adicionais.*' => [
                Rule::in([
                    'BAGAGEM',
                    'ESPORTES_AVENTURA',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'destino.required' => 'O destino é obrigatório.',
            'destino.in' => 'Destino inválido.',

            'data_inicio.required' => 'A data de início é obrigatória.',
            'data_inicio.date' => 'A data de início é inválida.',

            'data_fim.required' => 'A data de fim é obrigatória.',
            'data_fim.date' => 'A data de fim é inválida.',
            'data_fim.after_or_equal' => 'A data de fim deve ser maior ou igual à data de início.',

            'viajantes.required' => 'Informe ao menos um viajante.',
            'viajantes.array' => 'Os viajantes devem ser enviados em formato de lista.',
            'viajantes.min' => 'Informe ao menos um viajante.',

            'viajantes.*.nome.required' => 'O nome do viajante é obrigatório.',

            'viajantes.*.data_nascimento.required' => 'A data de nascimento do viajante é obrigatória.',
            'viajantes.*.data_nascimento.date' => 'A data de nascimento é inválida.',

            'viajantes.*.adicionais.*.in' => 'Adicional inválido.',
        ];
    }
}
