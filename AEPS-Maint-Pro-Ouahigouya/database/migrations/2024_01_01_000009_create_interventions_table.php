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
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('forage_id')->nullable()->constrained('forages')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('type', ['preventive', 'corrective', 'urgence', 'inspection', 'maintenance']);
            $table->string('titre');
            $table->text('description');
            $table->text('diagnostic')->nullable();
            $table->text('travaux_realises')->nullable();
            $table->date('date_intervention');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->integer('duree_minutes')->nullable();
            $table->decimal('cout_main_oeuvre', 10, 2)->default(0);
            $table->decimal('cout_pieces', 10, 2)->default(0);
            $table->decimal('cout_total', 10, 2)->default(0);
            $table->enum('statut', ['planifiee', 'en_cours', 'terminee', 'annulee'])->default('planifiee');
            $table->enum('priorite', ['faible', 'moyenne', 'haute', 'critique'])->default('moyenne');
            $table->text('recommandations')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index pour recherches et performances
            $table->index('type');
            $table->index('statut');
            $table->index('priorite');
            $table->index('date_intervention');
            $table->index(['site_id', 'date_intervention']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
