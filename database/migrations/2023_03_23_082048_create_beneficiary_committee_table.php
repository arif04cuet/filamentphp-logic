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
        Schema::create('beneficiary_committee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->unsignedBigInteger('committee_id')->nullable();
            $table->unsignedBigInteger('committee_role_id')->nullable();
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
        Schema::dropIfExists('beneficiary_committee');
    }
};
