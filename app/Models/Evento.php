<?php

namespace App\Models;

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Domain\Eventos\Enums\TipoEvento; // ✅ 1. Importa a nova Enum de Tipo
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\EventoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * @property \Carbon\Carbon $dt_evento
 * @property-read \App\Domain\Eventos\Enums\TipoEvento $tipo
 */
class Evento extends Model implements Auditable
{
    /** @use HasFactory<EventoFactory> */
    use HasFactory, AuditableTrait;

    protected $table = 'eventos';

    protected $fillable = [
        'evento',
        'tipo', // ✅ 2. Adicionado ao fillable
        'anotacoes',
        'congregacao_id',
        'dt_evento',
        'situacao',
    ];

    // ✅ 3. Define exatamente quais campos serão rastreados pela auditoria
    protected $auditable = [
        'evento',
        'tipo',
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
            'tipo' => TipoEvento::class, // ✅ 4. Cast para a Enum
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

    /**
     * Scope para filtrar eventos visíveis para um determinado usuário.
     */
    public function scopeVisivelPara(Builder $q, User $user): Builder
    {
        // Admin vê tudo
        if ($user->isAdminGeral()) {
            return $q;
        }

        // Usuários sem congregação vinculada não visualizam eventos
        if ($user->congregacao_id === null) {
            return $q->whereRaw('1 = 0');
        }

        // Líder vê apenas os da sua congregação
        if ($user->isLiderCongregacao()) {
            return $q->where('congregacao_id', $user->congregacao_id);
        }

        // Visualizador com congregação vinculada vê tudo
        return $q;
    }

    public function scopeDoMes(Builder $q, \Carbon\CarbonInterface $mes): Builder
    {
        $inicio = $mes->copy()->startOfMonth();
        $fim = $mes->copy()->endOfMonth();

        return $q->whereDate('dt_evento', '>=', $inicio->toDateString())
                 ->whereDate('dt_evento', '<=', $fim->toDateString());
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