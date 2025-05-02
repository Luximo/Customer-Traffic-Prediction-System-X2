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
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('temperature', 5, 2);
            $table->unsignedTinyInteger('condition'); // 0 = Clear, 1 = Cloudy, etc.
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
