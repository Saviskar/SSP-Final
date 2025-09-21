<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8">Shopping Cart</h1>
    
    <!-- Cart Content -->
    <div id="cart-content">
        <!-- Loading State -->
        <div id="cart-loading" class="flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
                <p class="text-gray-500">Loading your cart...</p>
            </div>
        </div>

        <!-- Empty Cart State -->
        <div id="empty-cart" class="text-center py-12 hidden">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-6">Looks like you haven't added any items to your cart yet.</p>
            <a href="allproducts.html" class="bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                Continue Shopping
            </a>
        </div>

        <!-- Cart Items -->
        <div id="cart-items" class="hidden">
            <!-- Cart Table -->
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm mb-8">
                <!-- Desktop Table Header -->
                <div class="hidden md:grid md:grid-cols-6 bg-gray-50 px-6 py-4 font-semibold text-gray-700 border-b border-gray-200">
                    <div class="col-span-3">Product</div>
                    <div class="col-span-1 text-center">Quantity</div>
                    <div class="col-span-1 text-center">Total</div>
                    <div class="col-span-1"></div>
                </div>

                <!-- Cart Items Container -->
                <div id="cart-items-container">
                    <!-- Items will be loaded dynamically -->
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <!-- Continue Shopping -->
                    <a href="allproducts.php" class="inline-flex items-center text-red-500 hover:text-red-600 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Continue Shopping
                    </a>
                </div>

                <div>
                    <!-- Cart Summary -->
                    <div id="cart-summary">
                        <!-- Summary will be loaded dynamically -->
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-6">
                        <button id="clear-cart" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors">
                            Clear Cart
                        </button>
                        <a href="checkout.php" class="flex-1 text-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg transition-colors">
                            Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="../assets/js/main.js"></script>
<script src="../assets/js/cart.js"></script>

</body>
</html>