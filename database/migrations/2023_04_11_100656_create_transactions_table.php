<?php

use App\Models\Investment;
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
        Schema::create('business_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Investment::class, 'business_id');
            $table->enum('type', ['income', 'expense']);
            $table->string('title')->nullable();
            $table->integer('code_id')->nullable();
            $table->date('transacion_date')->nullable();
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
        Schema::dropIfExists('business_transactions');
    }
};
