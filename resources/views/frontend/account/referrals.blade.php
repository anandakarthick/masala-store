@extends('layouts.app')

@section('title', 'My Referrals')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        @include('frontend.account.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1">
            <h1 class="text-2xl font-bold mb-6">
                <i class="fas fa-users text-blue-600 mr-2"></i>Refer & Earn
            </h1>

            @if($programInfo['enabled'])
                <!-- Referral Program Info -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white mb-6 shadow-lg">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-6">
                        <div class="flex-1">
                            <h2 class="text-xl font-bold mb-2">
                                <i class="fas fa-gift mr-2"></i>Refer Friends & Earn Rewards!
                            </h2>
                            <p class="text-blue-100 mb-4">
                                Share your unique referral code with friends. When they place 
                                {{ $programInfo['first_order_only'] ? 'their first order' : 'an order' }}, 
                                you earn <strong>{{ $programInfo['reward_text'] }}</strong>!
                            </p>
                            @if($programInfo['min_order_amount'] > 0)
                                <p class="text-sm text-blue-200">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Min. order amount: ₹{{ number_format($programInfo['min_order_amount'], 0) }}
                                </p>
                            @endif
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center min-w-[200px]">
                            <p class="text-blue-100 text-xs mb-1">Your Referral Code</p>
                            <p class="text-2xl font-bold tracking-wider mb-2">{{ $user->referral_code }}</p>
                            <button onclick="copyReferralCode()" class="bg-white text-blue-600 px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-blue-50 transition w-full">
                                <i class="fas fa-copy mr-1"></i> Copy Code
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Share Options -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="font-semibold mb-4">Share Your Referral Link</h3>
                    @php
                        $referralLink = url('/register?ref=' . $user->referral_code);
                    @endphp
                    <div class="flex flex-col md:flex-row gap-4 items-start">
                        <div class="flex-1">
                            <div class="flex">
                                <input type="text" value="{{ $referralLink }}" id="referralLink" readonly
                                       class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2 bg-gray-50 text-gray-700 text-sm">
                                <button onclick="copyReferralLink()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-lg transition">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="https://wa.me/?text={{ urlencode('Use my referral code ' . $user->referral_code . ' to get special discount! ' . $referralLink) }}" 
                               target="_blank" 
                               class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition"
                               title="Share on WhatsApp">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($referralLink) }}" 
                               target="_blank"
                               class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition"
                               title="Share on Facebook">
                                <i class="fab fa-facebook-f text-lg"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode('Use my referral code ' . $user->referral_code . ' to get special discount!') }}&url={{ urlencode($referralLink) }}" 
                               target="_blank"
                               class="w-10 h-10 bg-sky-500 hover:bg-sky-600 text-white rounded-full flex items-center justify-center transition"
                               title="Share on Twitter">
                                <i class="fab fa-twitter text-lg"></i>
                            </a>
                            <a href="mailto:?subject=Check out this store!&body={{ urlencode('Use my referral code ' . $user->referral_code . ' to get special discount! ' . $referralLink) }}" 
                               class="w-10 h-10 bg-gray-600 hover:bg-gray-700 text-white rounded-full flex items-center justify-center transition"
                               title="Share via Email">
                                <i class="fas fa-envelope text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <p class="text-gray-500 text-xs">Total Referrals</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_referrals'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <p class="text-gray-500 text-xs">Completed</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['completed_referrals'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <p class="text-gray-500 text-xs">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_referrals'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 text-center">
                        <p class="text-gray-500 text-xs">Total Earnings</p>
                        <p class="text-2xl font-bold text-blue-600">₹{{ number_format($stats['total_earnings'], 0) }}</p>
                    </div>
                </div>

                <!-- Referrals List -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b bg-gray-50">
                        <h2 class="font-semibold text-gray-800">Your Referrals</h2>
                    </div>

                    @if($referrals->count() > 0)
                        <div class="divide-y">
                            @foreach($referrals as $referral)
                                <div class="p-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $referral->referred->name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    Joined {{ $referral->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $referral->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $referral->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                {{ $referral->status === 'expired' ? 'bg-gray-100 text-gray-600' : '' }}">
                                                {{ ucfirst($referral->status) }}
                                            </span>
                                            @if($referral->reward_amount > 0)
                                                <p class="text-sm text-green-600 font-medium mt-1">
                                                    +₹{{ number_format($referral->reward_amount, 2) }} earned
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="p-4 border-t">
                            {{ $referrals->links() }}
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-3xl text-blue-500"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Referrals Yet</h3>
                            <p class="text-gray-500 mb-4">Share your code with friends to start earning!</p>
                        </div>
                    @endif
                </div>

                <!-- How It Works -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h3 class="font-semibold mb-4">How It Works</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-xl font-bold text-blue-600">1</span>
                            </div>
                            <h4 class="font-medium mb-1">Share Your Code</h4>
                            <p class="text-sm text-gray-500">Share your unique referral code with friends and family</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-xl font-bold text-green-600">2</span>
                            </div>
                            <h4 class="font-medium mb-1">Friend Signs Up</h4>
                            <p class="text-sm text-gray-500">They register using your code and place an order</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-3 bg-yellow-100 rounded-full flex items-center justify-center">
                                <span class="text-xl font-bold text-yellow-600">3</span>
                            </div>
                            <h4 class="font-medium mb-1">Earn Rewards</h4>
                            <p class="text-sm text-gray-500">You receive {{ $programInfo['reward_text'] }} in your wallet!</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Program Disabled -->
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-pause-circle text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Referral Program Not Active</h3>
                    <p class="text-gray-500">The referral program is currently not available. Please check back later!</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyReferralCode() {
    const code = '{{ $user->referral_code }}';
    navigator.clipboard.writeText(code).then(() => {
        alert('Referral code copied: ' + code);
    });
}

function copyReferralLink() {
    const link = document.getElementById('referralLink').value;
    navigator.clipboard.writeText(link).then(() => {
        alert('Referral link copied!');
    });
}
</script>
@endpush
@endsection
