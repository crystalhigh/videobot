<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vidpop_id')->nullable()->constrained('vidpops');
            $table->foreignUuid('step_id')->nullable()->constrained('steps');
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->foreignUuid('auto_responder_id')->nullable()->constrained('auto_responders');
            $table->string('type')->default('text');
            $table->text('data')->nullable();
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
        Schema::dropIfExists('replies');
    }
}
