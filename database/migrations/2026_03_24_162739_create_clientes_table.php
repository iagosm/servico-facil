<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
		public function up(): void
		{
				Schema::create('clientes', function (Blueprint $table) {
						$table->id();
						$table->string('nome', 100);
						$table->string('telefone', 20);
						$table->string('email', 150)->nullable()->index();
						$table->string('endereco')->nullable();
						$table->string('cidade')->nullable();
						$table->string('estado')->nullable();
						$table->string('cep')->nullable();
						$table->string('documento', 20)->nullable();
						$table->text('observacoes')->nullable();
						$table->timestamps();
				});
		}

		public function down(): void
		{
				Schema::dropIfExists('clientes');
		}
};