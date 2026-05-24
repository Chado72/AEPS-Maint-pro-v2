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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained('communes')->onDelete('restrict');
            $table->foreignId('village_id')->constrained('villages')->onDelete('restrict');
            $table->string('nom');
            $table->string('code')->unique()->nullable();
            $table->enum('type', ['AEPS', 'PEA'])->default('AEPS');
            $table->enum('statut', ['actif', 'en_panne', 'abandonne', 'en_construction'])->default('actif');
            $table->date('date_mise_en_service')->nullable();
            $table->string('capacite_reservoir')->nullable(); // en m³
            $table->integer('nombre_robinets')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('description')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index pour recherches et performances
            $table->index('nom');
            $table->index('type');
            $table->index('statut');
            $table->index(['commune_id', 'village_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
