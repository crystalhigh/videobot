<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVidpopupMetrics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vidpopup_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pv_id')->constrained('publisher_vidpopup')->onDelete('cascade');
            $table->string('status')->default('click'); // click or view
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vidpopup_metrics');
    }
}
