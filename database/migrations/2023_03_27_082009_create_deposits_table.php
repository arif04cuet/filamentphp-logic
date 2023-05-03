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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('association_id')->nullable();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 3)->nullable();
            $table->date('deposit_date')->nullable();
            $table->string('reference_id')->nullable();
            $table->json('file')->nullable();
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
        Schema::dropIfExists('deposits');
    }
};
