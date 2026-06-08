<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE messages MODIFY COLUMN status ENUM('scheduled','queued','sent','delivered','failed','spam','pending') NOT NULL DEFAULT 'queued'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE messages MODIFY COLUMN status ENUM('scheduled','queued','sent','delivered','failed','spam') NOT NULL DEFAULT 'queued'");
    }
};
