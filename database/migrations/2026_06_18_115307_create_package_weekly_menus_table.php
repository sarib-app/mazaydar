<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('package_weekly_menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('package_id');
            $table->string('week_start');
            $table->string('day_short');
            $table->string('day_full');
            $table->json('meals')->nullable();
            $table->timestamps();
            $table->unique(['package_id', 'week_start', 'day_short']);
        });
    }
    public function down(): void { Schema::dropIfExists('package_weekly_menus'); }
};
