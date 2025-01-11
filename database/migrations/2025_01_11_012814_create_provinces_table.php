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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('alt_name')->default('');
            $table->double('latitude')->default(0)->nullable(false);
            $table->double('longitude')->default(0)->nullable(false);
            $table->bigInteger('population')->default(0)->nullable(false);
            $table->enum('type_polygon', ['Polygon', 'MultiPolygon'])->default('Polygon');
            $table->longText('polygon')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
