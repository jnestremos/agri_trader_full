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
        Schema::create('supply_order_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('purchaseOrder_num');
            $table->integer('purchaseOrder_qtyDefect');
            $table->string('purchaseOrder_unit');            
            $table->string('refundOrder_status');
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
        Schema::dropIfExists('supply_order_refunds');
    }
};
