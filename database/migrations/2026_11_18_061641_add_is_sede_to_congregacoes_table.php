<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('congregacoes', function (Blueprint $table) {
            $table->boolean('is_sede')->default(false);
        });

        DB::table('congregacoes')
            ->where('id', 1)
            ->where('nome', 'SEDE')
            ->update(['is_sede' => true]);
    }

    public function down(): void
    {
        Schema::table('congregacoes', function (Blueprint $table) {
            $table->dropColumn('is_sede');
        });
    }
};