<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('final_date_id')->nullable()->constrained('event_dates')->onDelete('set null');
            $table->json('final_activities')->nullable(); // IDs des activités sélectionnées
            $table->text('summary_notes')->nullable();
            $table->boolean('is_finalized')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_summaries');
    }
};
