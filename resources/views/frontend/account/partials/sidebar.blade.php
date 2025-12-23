<aside class="lg:w-64 flex-shrink-0">
    <div class="bg-white rounded-lg shadow-md p-4">
        <!-- User Info -->
        <div class="flex items-center pb-4 border-b mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <span class="text-xl font-bold text-green-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <div class="ml-3">
                <p class="font-medium text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('account.dashboard') }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('account.dashboard') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-2">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.orders') }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('account.orders*') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-shopping-bag w-5"></i>
                        <span class="ml-2">My Orders</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.wallet') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm {{ request()->routeIs('account.wallet') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <span class="flex items-center">
                            <i class="fas fa-wallet w-5"></i>
                            <span class="ml-2">My Wallet</span>
                        </span>
                        @if(auth()->user()->wallet_balance > 0)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                ₹{{ number_format(auth()->user()->wallet_balance, 0) }}
                            </span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.referrals') }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('account.referrals') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-users w-5"></i>
                        <span class="ml-2">Refer & Earn</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.profile') }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('account.profile') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-user w-5"></i>
                        <span class="ml-2">Profile</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.password') }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('account.password') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-lock w-5"></i>
                        <span class="ml-2">Change Password</span>
                    </a>
                </li>
                <li class="pt-2 border-t mt-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 w-full">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="ml-2">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
