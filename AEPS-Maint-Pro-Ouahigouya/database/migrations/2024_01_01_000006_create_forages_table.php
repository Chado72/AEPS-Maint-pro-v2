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
        Schema::create('forages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('nom');
            $table->string('code')->unique()->nullable();
            $table->integer('profondeur')->nullable(); // en mètres
            $table->integer('debit')->nullable(); // en m³/h
            $table->date('date_forage')->nullable();
            $table->string('entreprise_forage')->nullable();
            $table->enum('statut', ['operationnel', 'en_panne', 'tari', 'abandonne'])->default('operationnel');
            $table->text('caracteristiques_geologiques')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index pour recherches
            $table->index('nom');
            $table->index('site_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forages');
    }
};
