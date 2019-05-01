<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldsItensFilaTable extends Migration
{

    public function __construct() {
        \Illuminate\Support\Facades\DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::table('itens_fila', function (Blueprint $table) {

            $table->unsignedBigInteger('id_musica')->after('id_user')->nullable(true);
            $table->foreign('id_musica')->references('id')->on('musicas');

        });

        $itensFila = \App\ItensFila::all();
        foreach ($itensFila as $item) {

            $musica = \App\Musica::where('spotify_uri', $item->spotify_uri)->first();
            $musicasComUri = \App\Musica::where('spotify_uri', $item->spotify_uri)->count();

            if (empty($musica) or $musica === null or $musicasComUri == 0) {
                if (!empty($item->spotify_uri)) {
                    $musica = new \App\Musica();
                    $musica->name = empty($item->name) ? ' ' : $item->name;
                    $musica->spotify_uri = $item->spotify_uri;
                    $musica->spotify_id = empty($item->spotify_id) ? ' ' : $item->spotify_id;
                    $musica->ms_duration = $item->ms_duration;
                    $musica->save();
                }

            }
            if (!empty($musica)) {
                $item->id_musica = $musica->id;
                $item->save();
            }
        }

        Schema::table('itens_fila', function (Blueprint $table) {

            $table->dropColumn('name');
            $table->dropColumn('spotify_uri');
            $table->dropColumn('spotify_id');
            $table->dropColumn('ms_duration');

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('itens_fila', function (Blueprint $table) {
            $table->string('name', 150)->after('id_user')->nullable(true)->default(null);
            $table->string('spotify_uri', 255)->after('name')->nullable(true)->default(null);
            $table->string('spotify_id', 255)->after('spotify_uri')->nullable(true);
            $table->integer('ms_duration')->after('spotify_uri')->nullable(true)->default(null);
        });

        $musicas = \App\Musica::all();
        foreach ($musicas as $musica) {
            $itensFila = \App\ItensFila::where('id_musica', $musica->id)->get();

            foreach($itensFila as $itemFila) {
                $itemFila->name = $musica->name;
                $itemFila->spotify_uri = $musica->spotify_uri;
                $itemFila->spotify_id = $musica->spotify_id;
                $itemFila->ms_duration = $musica->ms_duration;
                $itemFila->save();
            }
        }

        Schema::table('itens_fila', function (Blueprint $table) {
            $table->dropForeign(['id_musica']);
            $table->dropColumn('id_musica');
        });

        Schema::table('itens_fila', function (Blueprint $table) {
            $table->string('name', 150)->nullable(false)->change();
            $table->string('spotify_uri', 255)->nullable(false)->change();
            $table->integer('ms_duration')->nullable(false)->change();
        });
    }
}
