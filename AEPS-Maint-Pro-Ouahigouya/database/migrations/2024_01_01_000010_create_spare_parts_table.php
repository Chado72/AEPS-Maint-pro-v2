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
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('reference')->unique()->nullable();
            $table->text('description')->nullable();
            $table->enum('categorie', ['pompe', 'moteur', 'panneau_solaire', 'batterie', 'robinet', 'vanne', 'tuyauterie', 'electrique', 'autre']);
            $table->string('unite_mesure')->default('unité');
            $table->integer('stock_actuel')->default(0);
            $table->integer('stock_minimum')->default(5);
            $table->integer('stock_maximum')->nullable();
            $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->string('fournisseur')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index pour recherches
            $table->index('nom');
            $table->index('categorie');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
