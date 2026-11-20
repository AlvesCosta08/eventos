<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexControleAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Apenas Admin Geral pode acessar
        return $this->user()?->isAdminGeral() ?? false;
    }

    public function rules(): array
    {
        return [
            'ano' => [
                'sometimes',
                'integer',
                'between:1,9999',
            ],
        ];
    }
}