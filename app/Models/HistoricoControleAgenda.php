<?php

namespace App\Models;

use App\Domain\Agenda\Enums\ModoAgenda;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoControleAgenda extends Model
{
    protected $table = 'historico_controle_agendas';

    public $timestamps = false;

    protected $fillable = [
        'controle_agenda_id',
        'ano',
        'modo_anterior',
        'modo_novo',
        'alterado_por',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'ano' => 'integer',
            'modo_anterior' => ModoAgenda::class,
            'modo_novo' => ModoAgenda::class,
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $historico): void {
            if ($historico->created_at === null) {
                $historico->created_at = now();
            }
        });
    }

    public function controleAgenda(): BelongsTo
    {
        return $this->belongsTo(
            ControleAgenda::class,
            'controle_agenda_id'
        );
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'alterado_por'
        );
    }
}
