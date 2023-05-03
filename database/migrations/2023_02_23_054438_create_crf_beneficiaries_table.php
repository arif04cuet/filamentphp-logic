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
        Schema::create('crf_beneficiaries', function (Blueprint $table) {
            $table->id();

            $table->string('beneficiary_account_no')->nullable();
            $table->string('beneficiary_mobile')->nullable();
            $table->string('beneficiary_nid_br')->nullable();
            $table->string('beneficiary_name')->nullable();
            $table->string('district')->nullable();
            $table->string('division')->nullable();
            $table->string('father_name_spouse')->nullable();
            $table->string('hh')->nullable();
            $table->string('hh_head_mobile_no')->nullable();
            $table->string('hh_head_nid_no_br')->nullable();
            $table->string('hh_head_name')->nullable();
            $table->string('mobile_number_type')->nullable();
            $table->string('new_hh_id')->nullable();
            $table->smallInteger('round')->nullable();
            $table->string('union')->nullable();
            $table->string('upazila')->nullable();
            $table->string('village_name')->nullable();
            $table->string('ward')->nullable();
            $table->string('_id')->unique();
            $table->string('crf_group_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crf_beneficiaries');
    }
};
