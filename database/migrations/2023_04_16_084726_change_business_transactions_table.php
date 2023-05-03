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
        Schema::table('business_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('code_id')->nullable()->change();
            $table->dropColumn('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_transactions', function (Blueprint $table) {
            $table->integer('code_id')->nullable()->change();
            $table->string('title')->nullable();
        });
    }
};
