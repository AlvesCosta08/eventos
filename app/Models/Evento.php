<?php

namespace App\Models;

use App\Domain\Eventos\Enums\SituacaoEvento;
use Database\Factories\EventoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends Model
{
    /** @use HasFactory<EventoFactory> */
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'evento',
        'anotacoes',
        'congregacao_id',
        'dt_evento',
        'situacao',
    ];

    protected function casts(): array
    {
        return [
            'dt_evento' => 'date',
            'situacao' => SituacaoEvento::class,
        ];
    }

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function scopeFuturos(Builder $q): Builder
    {
        return $q->whereDate('dt_evento', '>=', now()->toDateString())
            ->orderBy('dt_evento');
    }

    public function scopeProximos(Builder $q, int $dias = 30): Builder
    {
        return $q->whereDate('dt_evento', '>=', now()->toDateString())
            ->whereDate('dt_evento', '<=', now()->addDays($dias)->toDateString());
    }

    public function scopeDaCongregacao(Builder $q, int $congregacaoId): Builder
    {
        return $q->where('congregacao_id', $congregacaoId);
    }

    public function scopeFilter(Builder $q, array $f): Builder
    {
        return $q
            ->when($f['busca'] ?? null, function (Builder $qq, $v) {
                $qq->where(function (Builder $inner) use ($v) {
                    $inner->where('evento', 'like', "%{$v}%")
                        ->orWhere('anotacoes', 'like', "%{$v}%");
                });
            })
            ->when($f['congregacao_id'] ?? null, fn (Builder $qq, $v) => $qq->where('congregacao_id', $v))
            ->when($f['situacao'] ?? null, fn (Builder $qq, $v) => $qq->where('situacao', $v))
            ->when($f['de'] ?? null, fn (Builder $qq, $v) => $qq->whereDate('dt_evento', '>=', $v))
            ->when($f['ate'] ?? null, fn (Builder $qq, $v) => $qq->whereDate('dt_evento', '<=', $v));
    }
}