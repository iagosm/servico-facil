<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            $table->enum('tipo', ['insumo', 'venda'])->default('insumo')->after('nome');
            $table->enum('condicao', ['novo', 'conservado', 'com_defeito', 'para_pecas'])
                  ->default('novo')
                  ->after('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            $table->dropColumn('tipo');
            $table->dropColumn('condicao');
        });
    }
};
