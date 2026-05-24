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
        Schema::create('solar_systems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('energy_source_id')->constrained('energy_sources')->onDelete('cascade');
            $table->integer('nombre_panneaux')->nullable();
            $table->integer('puissance_panneau')->nullable(); // en Wc
            $table->integer('capacite_batterie')->nullable(); // en Ah
            $table->integer('tension')->nullable(); // en V
            $table->string('marque_controleur')->nullable();
            $table->date('date_installation_panneaux')->nullable();
            $table->date('date_remplacement_batteries')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            
            $table->index('energy_source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solar_systems');
    }
};
