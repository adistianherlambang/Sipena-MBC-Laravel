<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->enum('sender_role', ['admin', 'super_admin', 'system'])->default('admin');
            $table->enum('visibility', ['internal', 'public'])->default('internal');
            $table->text('message');
            $table->string('attachment_file', 255)->nullable();

            $table->timestamps();

            $table->index('complaint_id');
            $table->index('visibility');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_responses');
    }
};
