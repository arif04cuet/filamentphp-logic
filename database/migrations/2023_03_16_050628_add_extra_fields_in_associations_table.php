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
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn('crf_group_id');
            $table->integer('deposit_type')->after('name')->nullable();
            $table->integer('share_type')->after('deposit_type')->nullable();
            $table->integer('beneficiary_no')->after('share_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->string('crf_group_id')->nullable();
            $table->dropColumn('deposit_type');
            $table->dropColumn('share_type');
            $table->dropColumn('beneficiary_no');
        });
    }
};
