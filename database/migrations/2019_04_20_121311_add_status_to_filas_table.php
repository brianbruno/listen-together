<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToFilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('filas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->after('id')->nullable(true);
            $table->longText('descricao')->after('name')->nullable(true);
            $table->enum('status', ['A', 'I'])->after('descricao')->default('A')->comment('A - Ativa/I - Inativa');
            $table->float('avaliacao')->after('status')->default(0);
            $table->string('capa_fila', 255)->after('status')->nullable(true);
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('filas', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('descricao');
            $table->dropColumn('avaliacao');
            $table->dropColumn('capa_fila');
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });
    }
}
