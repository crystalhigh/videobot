<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVidpopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vidpops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->string('brand')->nullable();
            $table->string('end_font')->nullable();
            $table->string('end_bg')->nullable();
            $table->string('end_color')->nullable();
            $table->string('end_position')->nullable();
            $table->string('title')->nullable();
            $table->string('content')->nullable();
            $table->boolean('is_custom')->default(false);
            $table->string('custom_text')->nullable();
            $table->string('custom_link')->nullable();
            $table->string('advanced', 1024)->nullable()->default('');
            $table->string('social', 1024)->nullable()->default('');
            $table->string('widget', 1024)->nullable()->default('');
            $table->string('code')->nullable();
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
        Schema::dropIfExists('vidpops');
    }
}
