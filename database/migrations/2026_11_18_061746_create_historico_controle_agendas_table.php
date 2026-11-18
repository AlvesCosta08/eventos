<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_controle_agendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('controle_agenda_id')
                ->constrained('controle_agendas')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('ano');

            $table->string('modo_anterior', 30);

            $table->string('modo_novo', 30);

            $table->foreignId('alterado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['ano', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_controle_agendas');
    }
};