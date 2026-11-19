<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Congregacao extends Model
{
    use HasFactory;

    protected $table = 'congregacoes';

    protected $fillable = [
        'nome',
        'is_sede',
    ];

    protected function casts(): array
    {
        return [
            'is_sede' => 'boolean',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    public function isSede(): bool
    {
        return $this->is_sede === true;
    }
}
