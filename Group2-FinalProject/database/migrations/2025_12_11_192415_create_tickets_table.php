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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            // Relationships (Foreign Keys)
            //Student
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            //Admin
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('cascade');
            //Ticket Details
            $table->string('subject', 255);
            $table->text('description');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Low');
            $table->enum('status',['Open','In Progress', 'Resolved', 'Closed'])->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
