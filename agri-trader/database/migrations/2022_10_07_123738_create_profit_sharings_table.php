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
        Schema::create('profit_sharings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('farm_owner_id')->constrained('farm_owners')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('produce_id')->constrained('produces')->onDelete('cascade')->onUpdate('cascade');
            $table->double('ar_totalSales');
            $table->double('ar_totalExpenses');
            $table->double('ar_ownerShare');
            $table->string('ar_ownerShareType');
            $table->double('ar_ownerPercentage')->nullable();
            $table->double('ar_profit');
            $table->string('ar_paymentMethod');
            $table->string('ar_accName');
            $table->string('ar_accNum')->nullable();
            $table->string('ar_bankName')->nullable();
            $table->date('ar_datePaid');
            $table->date('ar_approvedOn')->nullable();
            $table->string('ar_remark')->nullable();
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
        Schema::dropIfExists('profit_sharings');
    }
};
