<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->datetime('date_time');
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
            $table->index(['event_id', 'date_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_dates');
    }
};
