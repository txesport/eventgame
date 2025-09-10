<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('date_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_date_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('vote'); // true = ✅, false = ❌
            $table->timestamps();
            
            $table->unique(['event_date_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('date_votes');
    }
};
