<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateArTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_responders', function (Blueprint $table) {
            //
            $table->string('street');
            $table->string('zipcode');
            $table->string('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_responders', function (Blueprint $table) {
            //
            $table->dropColumn('street');
            $table->dropColumn('zipcode');
            $table->dropColumn('phone');
        });
    }
}
