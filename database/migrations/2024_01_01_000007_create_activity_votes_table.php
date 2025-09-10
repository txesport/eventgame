<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_activity_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('vote_type', ['yes','no','maybe'])->default('yes');
            $table->timestamps();
            $table->unique(['event_activity_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_activity_votes');
    }
};
