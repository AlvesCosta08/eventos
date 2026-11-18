<?php

namespace App\Models;

use App\Domain\Agenda\Enums\ModoAgenda;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ControleAgenda extends Model
{
    protected $table = 'controle_agendas';

    protected $fillable = [
        'ano',
        'modo',
        'alterado_por',
    ];

    protected function casts(): array
    {
        return [
            'ano' => 'integer',
            'modo' => ModoAgenda::class,
        ];
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alterado_por');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(
            HistoricoControleAgenda::class,
            'controle_agenda_id'
        );
    }
}
