<?php

namespace App\Models;

use Database\Factories\CongregacaoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Congregacao extends Model
{
    /** @use HasFactory<CongregacaoFactory> */
    use HasFactory;

    protected $table = 'congregacoes';

    protected $fillable = ['nome'];

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}