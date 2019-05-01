<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItensFilaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itens_fila', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_fila');
            $table->string('name', 150);
            $table->string('spotify_uri', 255);
            $table->integer('ms_duration');
            $table->enum('status', ['N', 'I', 'F'])->comment('N - Não Iniciada/I - Iniciada/F - Finalizada')->default('N');
            $table->timestamps();

            $table->foreign('id_fila')->references('id')->on('filas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('itens_fila');
    }
}
