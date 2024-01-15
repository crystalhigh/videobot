<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vidpop_id')->nullable()->constrained('vidpops');
            $table->string('video_url', 1024)->nullable();
            $table->string('thumb_url', 1024)->nullable();
            $table->string('video_note')->nullable();
            $table->text('video_cc')->nullable();
            $table->boolean('fit_video')->default(1);
            $table->string('description', 1024)->nullable();
            $table->string('next_step')->nullable();
            $table->string('overlay', 1024)->nullable();
            $table->text('answer')->nullable();
            $table->integer('video_delay')->default(2);
            $table->tinyInteger('data_collection')->default(1);
            $table->integer('index')->default(1);
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
        //
        Schema::dropIfExists('steps');
    }
}
