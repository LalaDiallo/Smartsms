<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spam_keywords', function (Blueprint $table) {
            // Supprimer la contrainte globale (un mot-clé ne doit pas être
            // unique toutes règles confondues, mais uniquement par règle)
            $table->dropUnique(['keyword']);

            // Ajouter l'unicité composite : le même mot-clé peut exister
            // dans plusieurs règles, mais pas deux fois dans la même règle
            $table->unique(['spam_rule_id', 'keyword'], 'spam_keywords_rule_keyword_unique');
        });
    }

    public function down(): void
    {
        Schema::table('spam_keywords', function (Blueprint $table) {
            $table->dropUnique('spam_keywords_rule_keyword_unique');
            $table->unique('keyword');
        });
    }
};
