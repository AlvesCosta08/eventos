<?php

namespace App\Domain\Eventos\DTOs;

use App\Domain\Eventos\Enums\SituacaoEvento;
use Carbon\Carbon;

class CreateEventoDTO
{
    public function __construct(
        public readonly string $evento,
        public readonly Carbon $dtEvento,
        public readonly int $congregacaoId,
        public readonly SituacaoEvento $situacao,
        public readonly ?string $anotacoes = null,
    ) {}

    /**
     * Factory method: cria um DTO a partir de um array validado pelo FormRequest.
     * Centraliza a conversão de tipos em um único lugar.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            evento: (string) $data['evento'],
            dtEvento: Carbon::parse($data['dt_evento']),
            congregacaoId: (int) $data['congregacao_id'],
            situacao: $data['situacao'] instanceof SituacaoEvento 
                ? $data['situacao'] 
                : SituacaoEvento::from($data['situacao']),
            anotacoes: isset($data['anotacoes']) ? (string) $data['anotacoes'] : null,
        );
    }
}