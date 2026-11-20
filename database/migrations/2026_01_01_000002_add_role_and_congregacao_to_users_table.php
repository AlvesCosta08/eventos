<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 30)->default('visualizador')->after('password');
                $table->index('role');
            });
        }

        if (! Schema::hasColumn('users', 'congregacao_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('congregacao_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('congregacoes')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'congregacao_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['congregacao_id']);
                $table->dropColumn('congregacao_id');
            });
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            });
        }
    }
};
