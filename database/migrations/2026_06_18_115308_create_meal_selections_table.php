<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('meal_selections', function (Blueprint $table) {
            $table->id();
            $table->uuid('subscription_id');
            $table->string('week_start')->nullable();
            $table->string('day_short')->nullable();
            $table->string('day_id')->nullable();
            $table->json('selections');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('meal_selections'); }
};
