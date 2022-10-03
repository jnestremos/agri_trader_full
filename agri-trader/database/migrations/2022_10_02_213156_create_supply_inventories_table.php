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
        Schema::create('supply_inventories', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade')->onUpdate('cascade');            
            $table->string('supply_name');            
            $table->string('supply_type');            
            $table->string('supply_for');
            $table->integer('supply_reorderLevel');
            $table->double('supply_qty');
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
        Schema::dropIfExists('supply_inventories');
    }
};
