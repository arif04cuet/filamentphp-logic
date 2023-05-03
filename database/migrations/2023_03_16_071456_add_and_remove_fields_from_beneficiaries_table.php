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
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn('beneficiary_account_no');
            $table->dropColumn('district');
            $table->dropColumn('division');
            $table->dropColumn('hh');
            $table->dropColumn('hh_head_mobile_no');
            $table->dropColumn('hh_head_nid_no_br');
            $table->dropColumn('hh_head_name');
            $table->dropColumn('mobile_number_type');
            $table->dropColumn('new_hh_id');
            $table->dropColumn('round');
            $table->dropColumn('union');
            $table->dropColumn('upazila');
            $table->dropColumn('ward');
            $table->dropColumn('_id');
            $table->dropColumn('crf_group_id');
            $table->string('mother_name')->nullable();
            $table->integer('age')->nullable();
            $table->string('occupation')->nullable();
            $table->json('applicant_photo')->nullable();
            $table->json('applicant_signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('beneficiary_account_no')->nullable();
            $table->string('district')->nullable();
            $table->string('division')->nullable();
            $table->string('hh')->nullable();
            $table->string('hh_head_mobile_no')->nullable();
            $table->string('hh_head_nid_no_br')->nullable();
            $table->string('hh_head_name')->nullable();
            $table->string('mobile_number_type')->nullable();
            $table->string('new_hh_id')->nullable();
            $table->smallInteger('round')->nullable();
            $table->string('union')->nullable();
            $table->string('upazila')->nullable();
            $table->string('ward')->nullable();
            $table->string('_id')->unique();
            $table->string('crf_group_id')->nullable();
            $table->dropColumn('mother_name');
            $table->dropColumn('age');
            $table->dropColumn('occupation');
            $table->dropColumn('applicant_photo');
            $table->dropColumn('applicant_signature');
        });
    }
};
