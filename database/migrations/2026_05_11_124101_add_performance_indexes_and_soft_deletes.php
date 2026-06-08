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
        // Index sur campagnes
        Schema::table('campagnes', function (Blueprint $table) {
            if (!Schema::hasIndex('campagnes', 'campagnes_client_id_index')) {
                $table->index('client_id');
            }
            if (!Schema::hasIndex('campagnes', 'campagnes_status_index')) {
                $table->index('status');
            }
            if (!Schema::hasIndex('campagnes', 'campagnes_archived_index')) {
                $table->index('archived');
            }
        });

        // Index + soft deletes sur messages
        Schema::table('messages', function (Blueprint $table) {
            $table->softDeletes();
            if (!Schema::hasIndex('messages', 'messages_campagnes_id_index')) {
                $table->index('campagnes_id');
            }
            if (!Schema::hasIndex('messages', 'messages_status_index')) {
                $table->index('status');
            }
            if (!Schema::hasIndex('messages', 'messages_channel_index')) {
                $table->index('channel');
            }
            if (!Schema::hasIndex('messages', 'messages_contact_id_index')) {
                $table->index('contact_id');
            }
        });

        // Index sur contacts
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasIndex('contacts', 'contacts_client_id_index')) {
                $table->index('client_id');
            }
            if (!Schema::hasIndex('contacts', 'contacts_status_index')) {
                $table->index('status');
            }
        });

        // Index sur groupes
        Schema::table('groupes', function (Blueprint $table) {
            if (!Schema::hasIndex('groupes', 'groupes_client_id_index')) {
                $table->index('client_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['archived']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['campagnes_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['channel']);
            $table->dropIndex(['contact_id']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('groupes', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
        });
    }
};
