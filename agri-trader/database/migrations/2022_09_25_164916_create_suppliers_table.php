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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trader_id')->constrained('traders')->onDelete('cascade')->onUpdate('cascade');
            $table->string('supplier_name');
            $table->string('supplier_bankName')->nullable();
            // $table->string('supplier_bankName');
            // $table->string('supplier_accName');
            $table->string('supplier_accName')->nullable();
            // $table->string('supplier_accNum');
            $table->string('supplier_accNum')->nullable();
            $table->string('supplier_otherName')->nullable();
            // $table->string('supplier_otherName');
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
        Schema::dropIfExists('suppliers');
    }
};
