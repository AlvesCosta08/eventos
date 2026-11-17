<?php

namespace App\Http\Requests;

use App\Domain\Eventos\Enums\SituacaoEvento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventoRequest extends FormRequest
{
    /**
     * Autoriza o cadastro de eventos.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user || !$user->podeGerenciarEventos()) {
            return false;
        }

        if ($user->isAdminGeral()) {
            return true;
        }

        return $user->isLiderCongregacao()
            && $user->congregacao_id !== null;
    }

    /**
     * Define as regras de validação.
     */
    public function rules(): array
    {
        return [
            'evento' => [
                'required',
                'string',
                'max:150',
            ],

            'anotacoes' => [
                'nullable',
                'string',
            ],

            'congregacao_id' => [
                'required',
                'integer',
                Rule::exists('congregacoes', 'id'),
            ],

            'dt_evento' => [
                'required',
                'date',
            ],

            'situacao' => [
                'required',
                Rule::enum(SituacaoEvento::class),
            ],
        ];
    }

    /**
     * Garante que o líder cadastre eventos
     * somente na própria congregação.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if (!$user || $user->isAdminGeral()) {
            return;
        }

        if (
            $user->isLiderCongregacao()
            && $user->congregacao_id !== null
        ) {
            $this->merge([
                'congregacao_id' => $user->congregacao_id,
            ]);
        }
    }
}
