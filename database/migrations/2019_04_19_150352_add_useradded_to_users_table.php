<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUseraddedToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itens_fila', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->after('id_fila')->nullable(true);

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
        Schema::table('itens_fila', function (Blueprint $table) {
            $table->dropColumn('id_user');
            $table->dropForeign(['id_user']);
        });
    }
}
