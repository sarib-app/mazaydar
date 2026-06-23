<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('package_id');
            $table->string('status')->default('active');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('pause_start')->nullable();
            $table->string('resume_date')->nullable();
            $table->integer('pauses_used')->default(0);
            $table->string('payment_method');
            $table->uuid('address_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('subscriptions'); }
};
