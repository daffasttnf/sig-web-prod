<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->string('river')->nullable();
            $table->string('water_quality')->nullable();
            $table->integer('ika')->nullable();
            $table->string('soil_type')->nullable();
            $table->string('soil_characteristics')->nullable();
            $table->integer('rainfall')->nullable();
            $table->string('rainfall_category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            //
        });
    }
};
