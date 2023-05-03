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
            $table->renameColumn('transacion_date', 'transaction_date');
            $table->decimal('amount', 10, 5)->after('title')->nullable();
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
            $table->renameColumn('transaction_date', 'transacion_date');
            $table->dropColumn('amount');
        });
    }
};
