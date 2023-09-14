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
        Schema::create('consultation_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            
    $table->unsignedBigInteger('student_id');
 
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->foreignId('professor_id')->constrained()->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('PENDING')->enum (['PENDING', 'APPROVED', 'REJECTED']); 
            $table->text('note')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('type', ['IN_PERSON', 'ONLINE'])->default('IN_PERSON');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_requests');
    }
};
