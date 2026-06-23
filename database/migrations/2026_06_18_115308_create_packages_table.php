<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('packages', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->float('price')->default(0);
            $table->integer('meals_per_day')->default(1);
            $table->integer('total_meals')->default(24);
            $table->integer('cycle_days')->default(24);
            $table->json('meal_types')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('packages'); }
};
