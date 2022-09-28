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
        Schema::create('supply_order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('purchaseOrder_num');            
            $table->string('purchaseOrder_paymentMethod');                                                       
            $table->string('purchaseOrder_paymentType');                                                       
            $table->string('purchaseOrder_bankName')->nullable();    
            $table->string('purchaseOrder_accNum')->nullable();   
            $table->string('purchaseOrder_accName');                  
            $table->double('purchaseOrder_dpAmount');                  
            $table->double('purchaseOrder_percentage');                  
            $table->double('purchaseOrder_balance');                  
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
        Schema::dropIfExists('supply_order_payments');
    }
};
