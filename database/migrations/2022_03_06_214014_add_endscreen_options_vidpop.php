<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndscreenOptionsVidpop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vidpops', function (Blueprint $table) {
            //
            $table->string('title_size')->default('2.5rem');
            $table->string('content_size')->default('1.5rem');
            $table->string('custom_color')->default('#ffffff');
            $table->string('custom_bgcolor')->default('#1998e4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vidpops', function (Blueprint $table) {
            //
            $table->dropColumn('title_size');
            $table->dropColumn('content_size');
            $table->dropColumn('custom_color');
            $table->dropColumn('custom_bgcolor');
        });
    }
}
