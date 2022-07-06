<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produce_trader', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trader_id')->constrained('traders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('produce_id')->constrained('produces')->onUpdate('cascade')->onDelete('cascade');
            $table->string('prod_name')->nullable();
            $table->double('prod_totalQty')->default('0');
            $table->integer('prod_numOfFarms')->default('0');
            $table->date('prod_lastDateOfHarvest')->nullable();
            $table->string('prod_details')->nullable();
            $table->integer('produce_numOfGrades')->nullable();
            $table->string('produce_yield_dateHarvestTo')->nullable();
            $table->string('prod_timeOfHarvest')->nullable();
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
        Schema::dropIfExists('produce_trader');
    }
};
