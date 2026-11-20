<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controle_agendas', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('ano')->unique();

            $table->string('modo', 30)
                ->default('bloqueada');

            $table->foreignId('alterado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controle_agendas');
    }
};
