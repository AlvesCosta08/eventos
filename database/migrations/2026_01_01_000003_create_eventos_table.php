<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eventos')) {
            return;
        }

        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('evento', 150);
            $table->text('anotacoes')->nullable();
            $table->foreignId('congregacao_id')->constrained('congregacoes');
            $table->date('dt_evento');
            $table->string('situacao', 30)->default('agendado');
            $table->timestamps();

            $table->index('dt_evento');
            $table->index('situacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
