<?php

namespace App\Models;

use App\Domain\Users\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atributos permitidos para atribuição em massa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'congregacao_id',
    ];

    /**
     * Atributos ocultos na serialização.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversão automática dos atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Congregação vinculada ao usuário.
     */
    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    /**
     * Identifica o Administrador Geral.
     */
    public function isAdminGeral(): bool
    {
        return $this->role === UserRole::AdminGeral;
    }

    /**
     * Identifica o Líder de Congregação.
     */
    public function isLiderCongregacao(): bool
    {
        return $this->role === UserRole::LiderCongregacao;
    }

    /**
     * Identifica o usuário Visualizador.
     */
    public function isVisualizador(): bool
    {
        return $this->role === UserRole::Visualizador;
    }

    /**
     * Verifica se o perfil pode gerenciar eventos.
     */
    public function podeGerenciarEventos(): bool
    {
        return $this->role->podeGerenciarEventos();
    }

    /**
     * Verifica se o perfil pode gerenciar usuários.
     */
    public function podeGerenciarUsuarios(): bool
    {
        return $this->role->podeGerenciarUsuarios();
    }

    /**
     * Verifica se o usuário pertence à congregação.
     */
    public function pertenceACongregacao(int $congregacaoId): bool
    {
        return $this->congregacao_id !== null
            && (int) $this->congregacao_id === $congregacaoId;
    }
}
