<?php

namespace App\Http\Requests;

use App\Domain\Eventos\Enums\SituacaoEvento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->podeGerenciarEventos();
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

    protected function prepareForValidation(): void
    {
        if (!$this->user()->isAdminGeral() && $this->user()->congregacao_id) {
            $this->merge(['congregacao_id' => $this->user()->congregacao_id]);
        }
    }
}