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
        Schema::create('expenditures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->string('exp_ORNum');
            $table->string('exp_type');
            $table->date('exp_dateFrom');
            $table->date('exp_dateTo');
            $table->string('exp_paymentType');
            $table->string('exp_accNum')->nullable();
            $table->string('exp_bankName')->nullable();
            $table->string('exp_accName');
            $table->double('exp_amount');
            $table->string('exp_remark');
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
        Schema::dropIfExists('expenditures');
    }
};
