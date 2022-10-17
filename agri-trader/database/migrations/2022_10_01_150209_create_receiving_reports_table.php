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
        Schema::create('receiving_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_num');
            $table->foreignId('supply_id')->constrained('supplies')->onUpdate('cascade')->onDelete('cascade');            
            $table->string('purchaseOrder_num');
            $table->integer('purchaseOrder_qtyGood');
            $table->integer('purchaseOrder_qtyDefect');
            $table->string('purchaseOrder_unit');
            $table->double('purchaseOrder_subTotal');
            $table->string('report_remark');
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
        Schema::dropIfExists('receiving_reports');
    }
};
