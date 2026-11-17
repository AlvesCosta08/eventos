<?php

namespace App\Http\Requests;

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Evento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventoRequest extends FormRequest
{
    /**
     * Verifica se o usuário pode editar o evento.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $evento = $this->route('evento');

        if (!$user || !$evento instanceof Evento) {
            return false;
        }

        return $user->can('update', $evento);
    }

    /**
     * Define as regras de validação da atualização.
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
     * Impede que líderes transfiram eventos
     * para outras congregações.
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
