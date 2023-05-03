<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_sectors', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']);
            $table->string('title');
            $table->string('code_id')->unique();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->timestamps();
        });

        // Insert the default rows
        DB::table('business_sectors')->insert([
            ['type' => 'income', 'title' => 'Income', 'code_id' => 'sector-1', 'parent_id' => 0],
            ['type' => 'expense', 'title' => 'Expense', 'code_id' => 'sector-2', 'parent_id' => 0],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_sectors');
    }
};
