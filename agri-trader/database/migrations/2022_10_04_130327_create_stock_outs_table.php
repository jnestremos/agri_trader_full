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
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade')->onUpdate('cascade');
            $table->double('supply_qty');
            $table->string('supply_unit');
            $table->string('stockOut_stage');
            $table->string('stockOut_remark');
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
        Schema::dropIfExists('stock_outs');
    }
};
