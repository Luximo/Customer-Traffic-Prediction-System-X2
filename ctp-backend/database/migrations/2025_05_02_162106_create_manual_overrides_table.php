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
        Schema::create('manual_overrides', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedTinyInteger('hour'); // Hour from 0 to 23
            $table->unsignedSmallInteger('value'); // Predicted customer count
            $table->timestamps();

            $table->unique(['date', 'hour']); // Prevent duplicate overrides for same slot
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_overrides');
    }
};
