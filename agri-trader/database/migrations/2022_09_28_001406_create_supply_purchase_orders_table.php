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
        Schema::create('supply_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trader_id')->constrained('traders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('purchaseOrder_num');
            $table->string('purchaseOrder_status');
            $table->integer('purchaseOrder_qty');
            $table->string('purchaseOrder_unit');
            $table->double('purchaseOrder_subTotal');
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
        Schema::dropIfExists('supply_purchase_orders');
    }
};
