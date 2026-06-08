<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index('complaint_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_logs');
    }
};
