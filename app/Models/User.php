<?php

namespace App\Models;

use App\Domain\Users\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;       // ✅ 1. Importa a interface
use OwenIt\Auditing\Auditable as AuditableTrait; // ✅ 2. Importa o Trait

/**
 * @property-read \App\Domain\Users\Enums\UserRole $role
 */
class User extends Authenticatable implements Auditable // ✅ 3. Implementa a interface
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, AuditableTrait; // ✅ 4. Adiciona o Trait

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'congregacao_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ 5. Define exatamente quais campos serão auditados (segurança e performance)
    // Note que 'password' e 'remember_token' foram excluídos propositalmente
    protected $auditable = [
        'name',
        'email',
        'role',
        'congregacao_id',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function isAdminGeral(): bool
    {
        return $this->role === UserRole::AdminGeral;
    }

    public function isLiderCongregacao(): bool
    {
        return $this->role === UserRole::LiderCongregacao;
    }

    public function isVisualizador(): bool
    {
        return $this->role === UserRole::Visualizador;
    }

    public function podeGerenciarEventos(): bool
    {
        return $this->role->podeGerenciarEventos();
    }

    public function podeGerenciarUsuarios(): bool
    {
        return $this->role->podeGerenciarUsuarios();
    }

    public function pertenceACongregacao(int $congregacaoId): bool
    {
        return $this->congregacao_id !== null
            && (int) $this->congregacao_id === $congregacaoId;
    }
}