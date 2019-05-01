<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTwitterTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('twitter_oauth_token', 200)->after('spotify_status')->nullable(true)->default(null);
            $table->string('twitter_oauth_token_secret', 200)->after('twitter_oauth_token')->nullable(true)->default(null);
            $table->string('twitter_user_id', 150)->after('twitter_oauth_token_secret')->nullable(true)->default(null);
            $table->string('twitter_screen_name', 100)->after('twitter_user_id')->nullable(true)->default(null);
            $table->boolean('twitter_status')->after('twitter_screen_name')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('twitter_oauth_token');
            $table->dropColumn('twitter_oauth_token_secret');
            $table->dropColumn('twitter_user_id');
            $table->dropColumn('twitter_screen_name');
            $table->dropColumn('twitter_status');
        });
    }
}
