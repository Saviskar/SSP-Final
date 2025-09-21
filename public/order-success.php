<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Main Content -->
<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <!-- Success Icon -->
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <!-- Success Message -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Order Placed Successfully!</h1>
        <p class="text-lg text-gray-600 mb-8">Thank you for your order. We've received your request and will process it soon.</p>

        <!-- Order Details -->
        <div id="order-details" class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
            
            <!-- Loading State -->
            <div id="details-loading" class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div>
                <span class="ml-3 text-gray-600">Loading order details...</span>
            </div>

            <!-- Order Info -->
            <div id="order-info" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Order Information</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium">Order ID:</span> <span id="order-id-display">#</span></p>
                            <p><span class="font-medium">Order Date:</span> <span id="order-date"></span></p>
                            <p><span class="font-medium">Status:</span> <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Processing</span></p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Payment Method</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p>Cash on Delivery (COD)</p>
                            <p>Payment will be collected upon delivery</p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div id="ordered-items">
                    <!-- Items will be loaded dynamically -->
                </div>

                <!-- Order Total -->
                <div id="order-total" class="border-t border-gray-200 pt-4 mt-4">
                    <!-- Total will be loaded dynamically -->
                </div>
            </div>
        </div>

        <!-- What's Next Section -->
        <div class="bg-blue-50 rounded-lg p-6 mb-8 text-left">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">What happens next?</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center mr-3 mt-0.5">1</div>
                    <p><strong>Order Confirmation:</strong> We'll send you an email confirmation shortly (if email notifications are enabled)</p>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center mr-3 mt-0.5">2</div>
                    <p><strong>Processing:</strong> Our team will prepare your order for shipment</p>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center mr-3 mt-0.5">3</div>
                    <p><strong>Delivery:</strong> Your order will be delivered to your specified address</p>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center mr-3 mt-0.5">4</div>
                    <p><strong>Payment:</strong> Pay the delivery person when your order arrives</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="allproducts.php" class="inline-flex items-center justify-center px-6 py-3 border border-red-500 text-red-500 font-medium rounded-lg hover:bg-red-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5" />
                </svg>
                Continue Shopping
            </a>
            <a href="orders.php" class="inline-flex items-center justify-center px-6 py-3 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                View All Orders
            </a>
        </div>
    </div>
</main>

<script src="../assets/js/main.js"></script>
<script>
// Get order ID from URL parameters
const urlParams = new URLSearchParams(window.location.search);
const orderId = urlParams.get('orderId');

document.addEventListener('DOMContentLoaded', function() {
    if (orderId) {
        loadOrderDetails(orderId);
    } else {
        showErrorState();
    }
});

async function loadOrderDetails(orderId) {
    const loadingEl = document.getElementById('details-loading');
    const orderInfoEl = document.getElementById('order-info');
    
    try {
        // Check authentication
        if (!window.petHaven.currentUser) {
            await window.petHaven.checkAuthStatus();
        }
        
        if (!window.petHaven.currentUser) {
            window.location.href = 'login.php';
            return;
        }
        
        // For now, we'll display the order ID and current date
        // In a full implementation, you'd fetch order details from the server
        document.getElementById('order-id-display').textContent = `#${orderId}`;
        document.getElementById('order-date').textContent = new Date().toLocaleDateString();
        
        // Hide loading and show order info
        loadingEl.classList.add('hidden');
        orderInfoEl.classList.remove('hidden');
        
        // Display placeholder order items (in real app, fetch from server)
        displayOrderPlaceholder();
        
    } catch (error) {
        console.error('Error loading order details:', error);
        showErrorState();
    }
}

function displayOrderPlaceholder() {
    // This is a placeholder. In a real application, you'd fetch the actual order details
    const orderedItemsEl = document.getElementById('ordered-items');
    const orderTotalEl = document.getElementById('order-total');
    
    orderedItemsEl.innerHTML = `
        <h3 class="font-medium text-gray-900 mb-3">Ordered Items</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                <div>
                    <p class="font-medium text-gray-900">Order items will be loaded here</p>
                    <p class="text-sm text-gray-600">Please check your email or contact support for detailed order information</p>
                </div>
            </div>
        </div>
    `;
    
    orderTotalEl.innerHTML = `
        <div class="flex justify-between items-center">
            <span class="text-lg font-semibold text-gray-900">Total:</span>
            <span class="text-lg font-semibold text-gray-900">Will be confirmed via email</span>
        </div>
    `;
}

function showErrorState() {
    const detailsEl = document.getElementById('order-details');
    detailsEl.innerHTML = `
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.768 0L3.046 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <p class="text-gray-600">Unable to load order details. Please contact support if you need assistance.</p>
        </div>
    `;
}

// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});

// Logout function
async function logout() {
    try {
        const response = await fetch('../process/logout.php');
        const result = await response.json();
        
        if (result.success) {
            window.petHaven.showMessage('Logged out successfully', 'success');
            setTimeout(() => {
                window.location.href = 'landing.php';
            }, 1000);
        }
    } catch (error) {
        console.error('Logout error:', error);
    }
}
</script>

</body>
</html>