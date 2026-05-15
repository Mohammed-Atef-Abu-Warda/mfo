<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // BUY, SELL, WAIT
            $table->decimal('price_at_signal', 10, 2);
            $table->integer('sentiment_score');
            $table->string('strength'); // Strong, Weak, Neutral
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
