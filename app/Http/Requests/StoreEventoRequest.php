<?php

namespace App\Http\Requests;

use App\Domain\Agenda\Services\ControleAgendaService;
use App\Domain\Eventos\Enums\SituacaoEvento;
use Carbon\Carbon;
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

        // Administrador Geral pode cadastrar
        // eventos independentemente do modo da agenda.
        if ($user->isAdminGeral()) {
            return true;
        }

        // Apenas líderes vinculados a uma congregação
        // podem cadastrar eventos.
        if (
            ! $user->isLiderCongregacao()
            || $user->congregacao_id === null
        ) {
            return false;
        }

        $data = $this->input('dt_evento');

        // Datas inválidas serão tratadas pelas
        // regras de validação, não pela autorização.
        if (! is_string($data)) {
            return true;
        }

        try {
            $dataEvento = Carbon::createFromFormat('!Y-m-d', $data);

            if (
                $dataEvento === false
                || $dataEvento->format('Y-m-d') !== $data
            ) {
                return true;
            }

            $ano = (int) $dataEvento->year;
        } catch (\Throwable $e) {
            return true;
        }

        // RN-001: consulta o modo da agenda
        // correspondente ao ano do evento.
        return app(ControleAgendaService::class)
            ->podeGerenciar(
                $user,
                $ano,
                (int) $user->congregacao_id
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
                'date',
                'date_format:Y-m-d',
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
