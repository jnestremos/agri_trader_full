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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trader_id')->constrained('traders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('contract_share_id')->constrained('contract_shares')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('produce_trader_id')->constrained('produce_trader')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('project_status_id')->constrained('project_statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('produce_id')->constrained('produces')->onDelete('cascade')->onUpdate('cascade');
            $table->string('farm_name');
            $table->double('contract_estimatedHarvest');
            $table->double('contract_estimatedPrice');
            $table->double('contract_estimatedSales');
            $table->double('contract_ownerShare');
            $table->double('contract_traderShare');
            $table->boolean('contract_status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};
