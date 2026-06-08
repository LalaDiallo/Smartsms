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
        Schema::create('spam_rules', function (Blueprint $table) {
            $table->id();
            $table->string('nom_regle', 255); //Nom de la règle (ex. : "Mots-clés promotionnels agressifs")
            $table->enum('type', ['keyword', 'pattern', 'frequency', 'sender', 'content'])->default('keyword'); //Type de règle (mots-clés, motifs, fréquence, expéditeur, contenu)
            $table->text('condition')->nullable(); //Condition de déclenchement de la règle
            $table->enum('action', ['block', 'quarantine', 'flag', 'review'])->default('block'); //Action à effectuer (bloquer, mettre en quarantaine, signaler, réviser)
            $table->enum('status', ['active', 'inactive'])->default('active'); //Statut de la règle (active ou inactive)
            $table->integer('matches')->default(0)->nullable(); //Nombre de fois que la règle a été déclenchée
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium'); //Sévérité de la règle (faible, moyenne, élevée, critique)
            $table->string('description', 500)->nullable(); //Description de la règle
            $table->boolean('auto_learn')->default(false); //Indique si la règle doit apprendre automatiquement des nouveaux motifs ou mots
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spam_rules');
    }
};
