<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\Referral;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    /**
     * Get wallet summary and transactions
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get summary
        $totalCredits = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');
            
        $totalDebits = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => (float) $user->wallet_balance,
                'total_earned' => (float) $totalCredits,
                'total_spent' => (float) $totalDebits,
                'transactions' => $transactions->items(),
            ],
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }
}
