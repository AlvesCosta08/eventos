<?php

namespace App\Http\Requests;

use App\Domain\Agenda\Services\ControleAgendaService;
use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventoRequest extends FormRequest
{
    /**
     * Autoriza a edição do evento conforme
     * a política e o controle anual da agenda.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $evento = $this->route('evento');

        if (! $user || ! ($evento instanceof Evento)) {
            return false;
        }

        // Preserva a autorização original.
        if (! $user->can('update', $evento)) {
            return false;
        }

        // Administrador Geral pode editar
        // independentemente do modo da agenda.
        if ($user->isAdminGeral()) {
            return true;
        }

        // Apenas líderes da própria congregação.
        if (
            ! $user->isLiderCongregacao()
            || $user->congregacao_id === null
            || ! $user->pertenceACongregacao(
                (int) $evento->congregacao_id
            )
        ) {
            return false;
        }

        $servico = app(ControleAgendaService::class);

        // RN-001: verifica o ano original.
        $anoOriginal = (int) $evento->dt_evento->year;

        if (! $servico->podeGerenciar(
            $user,
            $anoOriginal,
            (int) $evento->congregacao_id
        )) {
            return false;
        }

        // Verifica o ano de destino.
        $data = $this->input('dt_evento');

        // A validação tratará dados ausentes
        // ou datas com formato inválido.
        if (! is_string($data)) {
            return true;
        }

        try {
            $novaData = Carbon::createFromFormat(
                '!Y-m-d',
                $data
            );

            if (
                $novaData === false
                || $novaData->format('Y-m-d') !== $data
            ) {
                return true;
            }

            $anoDestino = (int) $novaData->year;
        } catch (\Throwable $e) {
            return true;
        }

        return $servico->podeGerenciar(
            $user,
            $anoDestino,
            (int) $evento->congregacao_id
        );
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
                'date_format:Y-m-d',
            ],

            'situacao' => [
                'required',
                Rule::enum(SituacaoEvento::class),
            ],
        ];
    }

    /**
     * Garante que o líder não transfira
     * eventos para outra congregação.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if (! $user || $user->isAdminGeral()) {
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
