<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInformationsToMusicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('musicas', function (Blueprint $table) {
            $table->integer('popularity')->nullable(true)->after('ms_duration');	
            $table->boolean('explicit')->nullable(true)->after('popularity');	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('musicas', function (Blueprint $table) {
            $table->dropColumn(['popularity', 'explicit']);
        });
    }
}
