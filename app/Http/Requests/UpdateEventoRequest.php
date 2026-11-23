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
        // ✅ Captura o ID do evento para ignorá-lo na validação de unicidade
        $eventoId = $this->route('evento')?->id;

        return [
            'evento' => ['required', 'string', 'max:150'],
            'tipo' => ['required', Rule::enum(TipoEvento::class)], // ✅ Novo campo
            'anotacoes' => ['nullable', 'string'],
            'congregacao_id' => ['required', 'integer', Rule::exists('congregacoes', 'id')],
            'dt_evento' => [
                'required',
                'date',
                'date_format:Y-m-d',
                // ✅ Regra de Negócio: Apenas um evento do tipo "Campo" por dia (ignorando o próprio evento em edição)
                function (string $attribute, mixed $value, Closure $fail) use ($eventoId) {
                    if ($this->input('tipo') === 'campo') {
                        $query = Evento::whereDate('dt_evento', $value)
                            ->where('tipo', 'campo');

                        if ($eventoId) {
                            $query->where('id', '!=', $eventoId);
                        }

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