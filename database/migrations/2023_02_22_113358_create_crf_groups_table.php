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
        Schema::create('crf_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('ward_0_value')->nullable();
            $table->string('ward_0_label')->nullable();
            $table->string('ward_1_value')->nullable();
            $table->string('ward_1_label')->nullable();
            $table->string('district')->nullable();
            $table->string('upazila')->nullable();
            $table->string('union')->nullable();
            $table->smallInteger('crf_round')->nullable();
            $table->string('group_name')->nullable();
            $table->string('group_address')->nullable();
            $table->smallInteger('male_beneficiaries')->nullable();
            $table->smallInteger('female_beneficiaries')->nullable();
            $table->smallInteger('crf_beneficiaries')->nullable();
            $table->string('livelihood_started')->nullable();
            $table->string('group_account_name')->nullable();
            $table->string('group_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('bank_branch_address')->nullable();
            $table->unsignedInteger('money_received')->nullable();
            $table->unsignedInteger('money_invested')->nullable();
            $table->string('remarks')->nullable();
            $table->string('bank_branch_name')->nullable();

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
        Schema::dropIfExists('crf_groups');
    }
};
