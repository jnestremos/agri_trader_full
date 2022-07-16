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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('project_status_id')->constrained('project_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->date('project_completionDate');
            $table->date('project_commenceDate');
            $table->date('project_floweringStart')->nullable();
            $table->date('project_floweringEnd')->nullable();
            $table->date('project_fruitBuddingStart')->nullable();
            $table->date('project_fruitBuddingEnd')->nullable();
            $table->date('project_devFruitStart')->nullable();
            $table->date('project_devFruitEnd')->nullable();
            $table->date('project_harvestableStart')->nullable();
            $table->date('project_harvestableEnd')->nullable();
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
        Schema::dropIfExists('projects');
    }
};
