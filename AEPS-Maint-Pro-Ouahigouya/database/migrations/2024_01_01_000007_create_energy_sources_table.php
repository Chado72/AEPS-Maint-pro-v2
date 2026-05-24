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
        Schema::create('energy_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->enum('type', ['solaire', 'electrique', 'diesel', 'eolien', 'manuel', 'gravitaire'])->default('solaire');
            $table->string('marque')->nullable();
            $table->string('modele')->nullable();
            $table->integer('puissance')->nullable(); // en W ou CV
            $table->date('date_installation')->nullable();
            $table->date('derniere_maintenance')->nullable();
            $table->enum('statut', ['operationnel', 'en_panne', 'maintenance', 'remplace'])->default('operationnel');
            $table->text('observations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index pour recherches
            $table->index('type');
            $table->index('site_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energy_sources');
    }
};
