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
        Schema::create('intervention_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intervention_id')->constrained('interventions')->onDelete('cascade');
            $table->foreignId('spare_part_id')->constrained('spare_parts')->onDelete('restrict');
            $table->integer('quantite_utilisee')->default(1);
            $table->decimal('prix_unitaire_applique', 10, 2)->nullable(); // Prix au moment de l'intervention
            $table->text('observations')->nullable();
            $table->timestamps();
            
            // Index pour performances
            $table->index('intervention_id');
            $table->index('spare_part_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_pieces');
    }
};
