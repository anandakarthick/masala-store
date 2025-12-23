@extends('layouts.admin')
@section('title', 'Referrals')
@section('page_title', 'Referral Management')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-500 text-sm">Total Referrals</div>
        <div class="text-2xl font-bold">{{ $stats['total_referrals'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-500 text-sm">Pending</div>
        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_referrals'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-500 text-sm">Completed</div>
        <div class="text-2xl font-bold text-green-600">{{ $stats['completed_referrals'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-500 text-sm">Total Rewards Paid</div>
        <div class="text-2xl font-bold text-blue-600">₹{{ number_format($stats['total_rewards_paid'], 0) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-500 text-sm">Active Referrers</div>
        <div class="text-2xl font-bold">{{ $stats['total_users_with_referrals'] }}</div>
    </div>
</div>

<!-- Process All Pending Button -->
@if($stats['pending_referrals'] > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ $stats['pending_referrals'] }} Pending Referral(s)
                </h3>
                <p class="text-sm text-yellow-700 mt-1">
                    These referrals have not been rewarded yet. Click to process all pending referrals that have valid orders.
                </p>
            </div>
            <form action="{{ route('admin.referrals.process-all-pending') }}" method="POST" class="ml-4">
                @csrf
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium"
                        onclick="return confirm('This will process rewards for all pending referrals with valid orders. Continue?')">
                    <i class="fas fa-play-circle mr-1"></i> Process All Pending
                </button>
            </form>
        </div>
    </div>
@endif

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('admin.referrals.index') }}" method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by referrer or referred name/email..."
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div>
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-search mr-1"></i> Filter
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.referrals.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                Clear
            </a>
        @endif
    </form>
</div>

<!-- Referrals Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referrer (A)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referred (B)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders Rewarded</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Reward</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">First Order</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($referrals as $referral)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                {{ strtoupper(substr($referral->referrer->name ?? 'N', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $referral->referrer->name ?? 'Deleted User' }}</div>
                                <div class="text-sm text-gray-500">{{ $referral->referrer->email ?? '' }}</div>
                                <div class="text-xs text-blue-600">Code: {{ $referral->referrer->referral_code ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                {{ strtoupper(substr($referral->referred->name ?? 'N', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $referral->referred->name ?? 'Deleted User' }}</div>
                                <div class="text-sm text-gray-500">{{ $referral->referred->email ?? '' }}</div>
                                @php
                                    $referredOrderCount = $referral->referred ? $referral->referred->orders()->whereNotIn('status', ['cancelled'])->count() : 0;
                                @endphp
                                <div class="text-xs {{ $referredOrderCount > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $referredOrderCount }} order(s)
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $referral->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $referral->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $referral->status === 'expired' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ ucfirst($referral->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $referral->orders_rewarded }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-medium text-green-600">₹{{ number_format($referral->reward_amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($referral->firstOrder)
                            <a href="{{ route('admin.orders.show', $referral->firstOrder) }}" class="text-blue-600 hover:underline">
                                #{{ $referral->firstOrder->order_number }}
                            </a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $referral->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center gap-2">
                            @if($referral->referrer)
                                <a href="{{ route('admin.referrals.user-wallet', $referral->referrer) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="View Wallet">
                                    <i class="fas fa-wallet"></i>
                                </a>
                            @endif
                            
                            @if($referral->status === 'pending')
                                {{-- Process Reward Button --}}
                                <form action="{{ route('admin.referrals.process-reward', $referral) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Process Reward"
                                            onclick="return confirm('Process referral reward for this user?')">
                                        <i class="fas fa-gift"></i>
                                    </button>
                                </form>
                                
                                {{-- Mark as Completed (without reward) --}}
                                <form action="{{ route('admin.referrals.mark-completed', $referral) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-800" title="Mark Completed (no reward)"
                                            onclick="return confirm('Mark this referral as completed without processing reward?')">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                
                                {{-- Mark as Expired --}}
                                <form action="{{ route('admin.referrals.mark-expired', $referral) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Mark as Expired"
                                            onclick="return confirm('Mark this referral as expired?')">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                        <p>No referrals found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($referrals->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $referrals->links() }}
        </div>
    @endif
</div>

<!-- Legend -->
<div class="mt-6 bg-white rounded-lg shadow-md p-4">
    <h4 class="font-semibold text-gray-700 mb-2">Action Icons Legend:</h4>
    <div class="flex flex-wrap gap-4 text-sm">
        <span><i class="fas fa-wallet text-blue-600 mr-1"></i> View Wallet</span>
        <span><i class="fas fa-gift text-green-600 mr-1"></i> Process Reward (credit referrer's wallet)</span>
        <span><i class="fas fa-check-circle text-gray-600 mr-1"></i> Mark Completed (without reward)</span>
        <span><i class="fas fa-times-circle text-red-600 mr-1"></i> Mark as Expired</span>
    </div>
</div>
@endsection
