<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldsToHistoricoMusicasTable extends Migration
{
    public function __construct() {
        \Illuminate\Support\Facades\DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historico_musicas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_musica')->after('uri')->nullable(true)->default(null);
            $table->foreign('id_musica')->references('id')->on('musicas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('historico_musicas', function (Blueprint $table) {
            $table->dropForeign(['id_musica']);
            $table->dropColumn('id_musica');
        });
    }
}
