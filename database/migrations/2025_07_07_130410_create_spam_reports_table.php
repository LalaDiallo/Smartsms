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
        Schema::create('spam_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['sms', 'email', 'whatsapp', 'push'])->default('sms'); //Type de message (SMS, email, WhatsApp, notification push)
            $table->text('content'); //Contenu du message détecté
            $table->string('sender', 255); //Expéditeur du message (ex. : numéro ou email)
            $table->string('recipient', 255); //Destinataire du message
            $table->text('reason');//Raison de la détection (ex. : "Mots-clés promotionnels détectés")');
            $table->enum('status', ['blocked', 'quarantined', 'flagged', 'reviewed'])->default('blocked'); //Statut du message (bloqué, en quarantaine, signalé, révisé)
            $table->dateTime('timestamp'); //('Horodatage du message');
            $table->integer('risk_score'); //Score de risque attribué au message (0 à 100)');
            $table->unsignedBigInteger('rule_id')->nullable(); //('Identifiant de la règle associée (clé étrangère)');
            $table->foreign('rule_id')->references('id')->on('spam_rules')->onDelete('cascade'); //('Relation avec la table des règles de spam');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spam_reports');
    }
};
