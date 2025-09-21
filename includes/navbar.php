<!-- Navbar -->
<header class="bg-white shadow-lg relative z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="landing.html" class="flex items-center text-xl font-bold text-gray-800">
                    <span class="text-red-500 mr-2">◆</span>
                    Pet Haven
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="allproducts.php" class="text-red-500 font-medium">Shop</a>
                <a href="#" class="text-gray-700 hover:text-red-500 font-medium transition-colors">Services</a>
                <a href="#" class="text-gray-700 hover:text-red-500 font-medium transition-colors">Community</a>
            </div>

            <!-- Desktop Search and Actions -->
            <div class="hidden md:flex items-center space-x-4">
                <div class="relative">
                    <input type="text" id="product-search" placeholder="Search products..." 
                           class="w-48 pl-4 pr-10 py-2 border-2 border-gray-200 rounded-full focus:border-red-500 outline-none transition-colors">
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
                <a href="cart.html" class="relative text-gray-600 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5" />
                    </svg>
                    <span class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                </a>
                <div class="auth-required hidden">
                    <button class="text-gray-600 hover:text-red-500 transition-colors" onclick="logout()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
                <div class="guest-only">
                    <a href="login.html" class="text-gray-600 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Mobile hamburger menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-700 hover:text-red-500 focus:outline-none focus:text-red-500 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-4 py-4 space-y-4 border-t">
                <a href="allproducts.html" class="block text-red-500 font-medium py-2">Shop</a>
                <a href="#" class="block text-gray-700 hover:text-red-500 font-medium py-2 transition-colors">Services</a>
                <a href="#" class="block text-gray-700 hover:text-red-500 font-medium py-2 transition-colors">Community</a>

                <!-- Mobile Search -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="relative">
                        <input type="text" placeholder="Search products..." 
                               class="w-full pl-4 pr-10 py-2 border-2 border-gray-200 rounded-full focus:border-red-500 outline-none transition-colors">
                    </div>
                </div>

                <!-- Mobile Actions -->
                <div class="flex space-x-4 pt-2">
                    <a href="cart.html" class="flex items-center space-x-2 text-gray-600 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5" />
                        </svg>
                        <span>Cart</span>
                    </a>
                    <div class="guest-only">
                        <a href="login.html" class="flex items-center space-x-2 text-gray-600 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Account</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>