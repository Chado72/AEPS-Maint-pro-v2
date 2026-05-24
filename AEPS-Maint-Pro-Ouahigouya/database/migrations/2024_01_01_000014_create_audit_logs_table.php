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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('model_type')->nullable(); // App\Models\Site, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_name')->nullable(); // Nom lisible du modèle
            $table->json('old_values')->nullable(); // Anciennes valeurs (avant modification)
            $table->json('new_values')->nullable(); // Nouvelles valeurs (après modification)
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Index pour recherches et performances
            $table->index('user_id');
            $table->index('action');
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
