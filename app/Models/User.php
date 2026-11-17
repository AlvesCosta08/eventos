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

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'congregacao_id',
    ];

    protected $hidden = ['password', 'remember_token'];

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
        return $this->congregacao_id === $congregacaoId;
    }
}