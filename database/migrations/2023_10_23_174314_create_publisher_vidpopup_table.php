<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublisherVidpopupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_vidpopup', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignUuid('creator_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignUuid('publisher_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignUuid('vidpopup_id')->nullable()->constrained('vidpops')->onDelete('cascade');
            $table->string('website_url');
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
        Schema::dropIfExists('publisher_vidpopup');
    }
}
