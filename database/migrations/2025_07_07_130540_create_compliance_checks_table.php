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
        Schema::create('compliance_checks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); //Nom de la vérification (ex. : "Consentement RGPD")');
            $table->enum('regulation', ['RGPD', 'CAN-SPAM', 'CASL', 'TCPA', 'DMA'])->default('RGPD'); //'Réglementation concernée');
            $table->enum('status', ['compliant', 'warning', 'violation'])->default('compliant');  //Statut de conformité (conforme, avertissement, violation)
            $table->date('last_check'); //Date de la dernière vérification');
            $table->integer('score'); //Score de conformité (0 à 100)');
            $table->text('issues')->nullable(); //Liste des problèmes détectés (tableau de texte ou JSON)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_checks');
    }
};
