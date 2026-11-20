<?php

namespace App\Http\Requests;

use App\Domain\Agenda\Enums\ModoAgenda;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateControleAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Apenas Admin Geral pode acessar
        return $this->user()?->isAdminGeral() ?? false;
    }

    public function rules(): array
    {
        return [
            'modo' => [
                'required',
                Rule::enum(ModoAgenda::class),
            ],
        ];
    }
}