<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_correct_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_record_id')->constrained()->cascadeOnDelete();
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('comment')->nullable();
            $table->string('status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_correct_requests');
    }
};
