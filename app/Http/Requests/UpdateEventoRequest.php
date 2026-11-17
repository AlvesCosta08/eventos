<?php

namespace App\Http\Requests;

use App\Domain\Eventos\Enums\SituacaoEvento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('evento'));
    }

    public function rules(): array
    {
        return [
            'evento' => ['required', 'string', 'max:150'],
            'anotacoes' => ['nullable', 'string'],
            'congregacao_id' => ['required', 'exists:congregacoes,id'],
            'dt_evento' => ['required', 'date'],
            'situacao' => ['required', Rule::enum(SituacaoEvento::class)],
        ];
    }
}