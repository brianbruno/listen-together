<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrivacidadeToFilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('filas', function (Blueprint $table) {
            $table->boolean('public')->after('descricao')->default(true);
            $table->boolean('colaborativa')->after('public')->default(true);
            $table->boolean('capa_dinamica')->after('colaborativa')->default(true);
            $table->string('spotify_uri', 255)->after('public')->nullable(true)->default(null);
            $table->string('spotify_id', 255)->after('spotify_uri')->nullable(true)->default(null);
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
            $table->dropColumn('public');
            $table->dropColumn('colaborativa');
            $table->dropColumn('spotify_uri');
            $table->dropColumn('spotify_id');
            $table->dropColumn('capa_dinamica');
        });
    }
}
