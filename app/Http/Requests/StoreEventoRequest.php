<?php

namespace App\Http\Requests;

use App\Domain\Agenda\Services\ControleAgendaService;
use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Domain\Eventos\Enums\TipoEvento;
use App\Models\Evento;
use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventoRequest extends FormRequest
{
    /**
     * Autoriza o cadastro de eventos conforme
     * o perfil e o controle anual da agenda.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user || ! $user->podeGerenciarEventos()) {
            return false;
        }

        if ($user->isAdminGeral()) {
            return true;
        }

        if (
            ! $user->isLiderCongregacao()
            || $user->congregacao_id === null
        ) {
            return false;
        }

        $data = $this->input('dt_evento');

        if (! is_string($data)) {
            return true;
        }

        try {
            $dataEvento = Carbon::createFromFormat('!Y-m-d', $data);

            if (
                $dataEvento === null
                || $dataEvento->format('Y-m-d') !== $data
            ) {
                return true;
            }

            $ano = (int) $dataEvento->year;
        } catch (\Throwable $e) {
            return true;
        }

        return app(ControleAgendaService::class)
            ->podeGerenciar(
                $user,
                $ano,
                (int) $user->congregacao_id
            );
    }

    public function rules(): array
    {
        return [
            'evento' => ['required', 'string', 'max:150'],
            'tipo' => ['required', Rule::enum(TipoEvento::class)], // ✅ Novo campo
            'anotacoes' => ['nullable', 'string'],
            'congregacao_id' => ['required', 'integer', Rule::exists('congregacoes', 'id')],
            'dt_evento' => [
                'required',
                'date',
                'date_format:Y-m-d',
                // ✅ Regra de Negócio: Apenas um evento do tipo "Campo" por dia
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($this->input('tipo') === 'campo') {
                        $query = Evento::whereDate('dt_evento', $value)
                            ->where('tipo', 'campo');

                        if ($query->exists()) {
                            $fail('Já existe um evento do tipo "Campo" agendado para esta data. Apenas um evento de Campo é permitido por dia.');
                        }
                    }
                },
            ],
            'situacao' => ['required', Rule::enum(SituacaoEvento::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if (! $user || $user->isAdminGeral()) {
            return;
        }

        if ($user->isLiderCongregacao() && $user->congregacao_id !== null) {
            $this->merge([
                'congregacao_id' => $user->congregacao_id,
            ]);
        }
    }
}