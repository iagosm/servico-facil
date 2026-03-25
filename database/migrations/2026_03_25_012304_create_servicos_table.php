<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supervisor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('tipo', 20);
            $table->string('status', 30)->default('recebido');
            $table->string('prioridade', 30)->default('normal');
            $table->text('obs_internas')->nullable();
            $table->text('obs_cliente')->nullable();
            $table->decimal('valor_cobrado', 10, 2)->default(0);
            $table->decimal('custo_total', 10, 2)->default(0);
            $table->date('data_entrada');
            $table->date('data_previsao')->nullable();
            $table->date('data_conclusao')->nullable();
            $table->date('data_entrega')->nullable();
            $table->date('validade_orcamento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
