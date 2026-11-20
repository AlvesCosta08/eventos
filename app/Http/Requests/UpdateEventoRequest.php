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
    public function authorize(): bool
    {
        $user = $this->user();
        $evento = $this->route('evento');

        if (! $user || ! ($evento instanceof Evento)) {
            return false;
        }

        if (! $user->can('update', $evento)) {
            return false;
        }

        if ($user->isAdminGeral()) {
            return true;
        }

        if (
            ! $user->isLiderCongregacao()
            || $user->congregacao_id === null
            || ! $user->pertenceACongregacao((int) $evento->congregacao_id)
        ) {
            return false;
        }

        $servico = app(ControleAgendaService::class);
        $anoOriginal = (int) $evento->dt_evento->year;

        if (! $servico->podeGerenciar($user, $anoOriginal, (int) $evento->congregacao_id)) {
            return false;
        }

        $data = $this->input('dt_evento');

        if (! is_string($data)) {
            return true;
        }

        try {
            $novaData = Carbon::createFromFormat('!Y-m-d', $data);

            // ✅ CORREÇÃO: Comparar com null em vez de false
            if (
                $novaData === null
                || $novaData->format('Y-m-d') !== $data
            ) {
                return true;
            }

            $anoDestino = (int) $novaData->year;
        } catch (\Throwable $e) {
            return true;
        }

        return $servico->podeGerenciar($user, $anoDestino, (int) $evento->congregacao_id);
    }

    public function rules(): array
    {
        return [
            'evento' => ['required', 'string', 'max:150'],
            'anotacoes' => ['nullable', 'string'],
            'congregacao_id' => ['required', 'integer', Rule::exists('congregacoes', 'id')],
            'dt_evento' => ['required', 'date_format:Y-m-d'],
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