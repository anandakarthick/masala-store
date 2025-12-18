<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // This migration is skipped - jobs table already exists from Laravel's default migration
    public function up(): void
    {
        // Do nothing - table already exists
    }

    public function down(): void
    {
        // Do nothing
    }
};
