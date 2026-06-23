<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('weekly_menu', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('day_short');
            $table->string('day_full');
            $table->json('meals')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('weekly_menu'); }
};
