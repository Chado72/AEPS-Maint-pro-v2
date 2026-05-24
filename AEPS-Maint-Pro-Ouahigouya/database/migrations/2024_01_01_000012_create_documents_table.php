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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('cascade');
            $table->foreignId('forage_id')->nullable()->constrained('forages')->onDelete('cascade');
            $table->foreignId('intervention_id')->nullable()->constrained('interventions')->onDelete('cascade');
            $table->string('nom_fichier');
            $table->string('nom_original');
            $table->string('chemin_fichier');
            $table->string('mime_type');
            $table->integer('taille_octets');
            $table->enum('type', ['photo', 'rapport', 'facture', 'plan', 'manuel', 'autre']);
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // uploader
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index pour recherches
            $table->index('type');
            $table->index('site_id');
            $table->index('intervention_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
