<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMembershipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('label', 20);
            $table->string('name', 50);
            $table->string('jv_produc_id', 50)->nullable();
            $table->string('cb_product_id', 50)->nullable();
            $table->string('email_subject')->nullable();
            $table->text('email_content')->nullable();
            $table->string('jv_callback', 512)->nullable();
            $table->string('cb_callback', 512)->nullable();
            $table->float('credits')->default(0);
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
        Schema::dropIfExists('memberships');
    }
}
