<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // messages — filtrés par campagne+statut à chaque envoi/analytics
        Schema::table('messages', function (Blueprint $table) {
            if (!$this->indexExists('messages', 'idx_messages_campagne_status')) {
                $table->index(['campagnes_id', 'status'], 'idx_messages_campagne_status');
            }
            if (!$this->indexExists('messages', 'idx_messages_sent_at')) {
                $table->index('sent_at', 'idx_messages_sent_at');
            }
            if (!$this->indexExists('messages', 'idx_messages_created_at')) {
                $table->index('created_at', 'idx_messages_created_at');
            }
            if (!$this->indexExists('messages', 'idx_messages_channel')) {
                $table->index('channel', 'idx_messages_channel');
            }
        });

        // campagnes — filtrés par client+statut dans analytics et dashboard
        Schema::table('campagnes', function (Blueprint $table) {
            if (!$this->indexExists('campagnes', 'idx_campagnes_client_status')) {
                $table->index(['client_id', 'status'], 'idx_campagnes_client_status');
            }
            if (!$this->indexExists('campagnes', 'idx_campagnes_created_at')) {
                $table->index('created_at', 'idx_campagnes_created_at');
            }
        });

        // subscriptions — lookup abonnement actif très fréquent
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!$this->indexExists('subscriptions', 'idx_subscriptions_client_status')) {
                $table->index(['client_id', 'status'], 'idx_subscriptions_client_status');
            }
            if (!$this->indexExists('subscriptions', 'idx_subscriptions_end_date')) {
                $table->index('end_date', 'idx_subscriptions_end_date');
            }
        });

        // contacts — comptage par client dans analytics
        Schema::table('contacts', function (Blueprint $table) {
            if (!$this->indexExists('contacts', 'idx_contacts_client_status')) {
                $table->index(['client_id', 'status'], 'idx_contacts_client_status');
            }
        });

        // responses — jointure avec messages dans analytics
        Schema::table('responses', function (Blueprint $table) {
            if (!$this->indexExists('responses', 'idx_responses_message_id')) {
                $table->index('message_id', 'idx_responses_message_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages',      fn (Blueprint $t) => $t->dropIndexIfExists('idx_messages_campagne_status'));
        Schema::table('messages',      fn (Blueprint $t) => $t->dropIndexIfExists('idx_messages_sent_at'));
        Schema::table('messages',      fn (Blueprint $t) => $t->dropIndexIfExists('idx_messages_created_at'));
        Schema::table('messages',      fn (Blueprint $t) => $t->dropIndexIfExists('idx_messages_channel'));
        Schema::table('campagnes',     fn (Blueprint $t) => $t->dropIndexIfExists('idx_campagnes_client_status'));
        Schema::table('campagnes',     fn (Blueprint $t) => $t->dropIndexIfExists('idx_campagnes_created_at'));
        Schema::table('subscriptions', fn (Blueprint $t) => $t->dropIndexIfExists('idx_subscriptions_client_status'));
        Schema::table('subscriptions', fn (Blueprint $t) => $t->dropIndexIfExists('idx_subscriptions_end_date'));
        Schema::table('contacts',      fn (Blueprint $t) => $t->dropIndexIfExists('idx_contacts_client_status'));
        Schema::table('responses',     fn (Blueprint $t) => $t->dropIndexIfExists('idx_responses_message_id'));
    }

    private function indexExists(string $table, string $name): bool
    {
        return collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->contains('Key_name', $name);
    }
};
