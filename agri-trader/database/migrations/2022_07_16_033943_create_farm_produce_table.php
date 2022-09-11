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
        Schema::create('farm_produce', function (Blueprint $table) {
            $table->foreignId('farm_id')->constrained('farms')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('produce_trader_id')->constrained('produce_trader')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('produce_id')->constrained('produces')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreignId('produce_inventory_id')->nullable()->constrained('produce_inventories')->onUpdate('cascade')->onDelete('cascade');
            $table->double('on_hand_latestPrice')->default('0');
            $table->double('on_hand_qty')->default('0');
            $table->date('prod_lastDateOfHarvest')->nullable();
            $table->string('farm_name')->nullable();            
            $table->string('prod_name')->nullable();            
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
        Schema::dropIfExists('farm_produce');
    }
};
