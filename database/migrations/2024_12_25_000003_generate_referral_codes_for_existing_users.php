<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Generate referral codes for existing users who don't have one
        User::whereNull('referral_code')
            ->orWhere('referral_code', '')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    do {
                        $code = strtoupper(Str::random(8));
                    } while (User::where('referral_code', $code)->exists());

                    $user->update(['referral_code' => $code]);
                }
            });
    }

    public function down(): void
    {
        // Nothing to reverse
    }
};
