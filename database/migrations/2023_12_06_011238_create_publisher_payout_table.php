<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublisherPayoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::create('publisher_payout', function (Blueprint $table) {
				$table->id();
				$table->foreignUuid('publisher_id')->constrained('users')->onDelete('cascade');
				$table->float('withdraw', 10)->default(0);
				$table->string('status')->default('Pending'); // Pending / Approved
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
			Schema::dropIfExists('publisher_payout');
    }
}
