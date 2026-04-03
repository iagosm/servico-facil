<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estoque_id')->nullable()->constrained('estoque')->nullOnDelete();
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->nullOnDelete();
            $table->foreignId('solicitado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recebido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('descricao', 150);
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 10, 2)->nullable();
            $table->enum('status', ['pendente', 'pedido', 'recebido', 'cancelado'])->default('pendente');
            $table->string('numero_pedido', 60)->nullable();
            $table->date('data_solicitacao')->nullable();
            $table->date('data_pedido')->nullable();
            $table->date('data_previsao')->nullable();
            $table->date('data_recebimento')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
