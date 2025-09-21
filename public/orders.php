<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <nav class="flex text-sm overflow-x-auto whitespace-nowrap scrollbar-hide">
        <a href="landing.php" class="text-red-500 hover:text-red-600 flex-shrink-0">Home</a>
        <span class="mx-2 text-gray-500 flex-shrink-0">/</span>
        <span class="text-gray-900 flex-shrink-0">My Orders</span>
    </nav>
</div>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
            <p class="text-gray-600 mt-1">Track and manage your pet supply orders</p>
        </div>
        <a href="allproducts.php" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-colors self-start sm:self-auto">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Shop More
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 mb-6 overflow-x-auto whitespace-nowrap scrollbar-hide pb-2">
        <button class="order-filter px-4 py-2 bg-red-100 text-red-600 rounded-full text-sm font-medium flex-shrink-0" data-status="">
            All Orders
        </button>
        <button class="order-filter px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-full text-sm font-medium flex-shrink-0" data-status="processing">
            Processing
        </button>
        <button class="order-filter px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-full text-sm font-medium flex-shrink-0" data-status="shipped">
            Shipped
        </button>
        <button class="order-filter px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-full text-sm font-medium flex-shrink-0" data-status="delivered">
            Delivered
        </button>
        <button class="order-filter px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-full text-sm font-medium flex-shrink-0" data-status="cancelled">
            Cancelled
        </button>
    </div>

    <!-- Orders Content -->
    <div id="orders-content">
        <!-- Loading State -->
        <div id="orders-loading" class="flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
                <p class="text-gray-500">Loading your orders...</p>
            </div>
        </div>

        <!-- Empty State -->
        <div id="no-orders" class="hidden text-center py-12">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">No orders found</h2>
            <p class="text-gray-500 mb-6">You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="allproducts.php" class="inline-flex items-center px-6 py-3 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5" />
                </svg>
                Start Shopping
            </a>
        </div>

        <!-- Orders List -->
        <div id="orders-list" class="hidden space-y-4">
            <!-- Orders will be loaded dynamically -->
        </div>
    </div>
</main>

<!-- Order Details Modal -->
<div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Order Details</h3>
                <button id="close-order-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="order-modal-content" class="p-6">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
let currentOrders = [];
let filteredOrders = [];

// Load orders when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    loadUserOrders();
});

function setupEventListeners() {
    // Filter buttons
    document.querySelectorAll('.order-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active filter UI
            document.querySelectorAll('.order-filter').forEach(b => {
                b.classList.remove('bg-red-100', 'text-red-600');
                b.classList.add('bg-gray-100', 'text-gray-600');
            });
            
            this.classList.remove('bg-gray-100', 'text-gray-600');
            this.classList.add('bg-red-100', 'text-red-600');
            
            const status = this.dataset.status;
            filterOrders(status);
        });
    });

    // Search functionality
    const searchInput = document.getElementById('order-search');
    if (searchInput) {
        searchInput.addEventListener('input', window.petHaven.debounce(function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            searchOrders(searchTerm);
        }, 300));
    }

    // Modal close
    document.getElementById('close-order-modal').addEventListener('click', closeOrderModal);
    document.getElementById('order-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeOrderModal();
        }
    });

    // Mobile menu
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
}

async function loadUserOrders() {
    const loadingEl = document.getElementById('orders-loading');
    const noOrdersEl = document.getElementById('no-orders');
    const ordersListEl = document.getElementById('orders-list');

    try {
        loadingEl.classList.remove('hidden');
        noOrdersEl.classList.add('hidden');
        ordersListEl.classList.add('hidden');

        // Check authentication
        if (!window.petHaven.currentUser) {
            await window.petHaven.checkAuthStatus();
        }

        if (!window.petHaven.currentUser) {
            window.location.href = 'login.php';
            return;
        }

        // Load orders
        const response = await fetch('../process/orders.php');
        const result = await response.json();

        loadingEl.classList.add('hidden');

        if (result.success && result.data.length > 0) {
            currentOrders = result.data;
            filteredOrders = [...currentOrders];
            displayOrders(filteredOrders);
            ordersListEl.classList.remove('hidden');
        } else {
            noOrdersEl.classList.remove('hidden');
        }

    } catch (error) {
        console.error('Error loading orders:', error);
        loadingEl.classList.add('hidden');
        noOrdersEl.classList.remove('hidden');
        window.petHaven.showMessage('Failed to load orders', 'error');
    }
}

function displayOrders(orders) {
    const ordersListEl = document.getElementById('orders-list');
    
    if (orders.length === 0) {
        ordersListEl.innerHTML = '<div class="text-center py-8 text-gray-500">No orders match your current filter.</div>';
        return;
    }

    ordersListEl.innerHTML = orders.map(order => {
        const statusClasses = {
            'processing': 'bg-yellow-100 text-yellow-800',
            'shipped': 'bg-blue-100 text-blue-800',
            'delivered': 'bg-green-100 text-green-800',
            'cancelled': 'bg-red-100 text-red-800'
        };

        const statusClass = statusClasses[order.Status] || 'bg-gray-100 text-gray-800';
        const itemsText = order.items.map(item => `${item.ProductName} x ${item.Quantity}`).join(', ');

        return `
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showOrderDetails(${order.OrderID})">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mb-3">
                            <h3 class="font-semibold text-gray-900">Order #${order.OrderID}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass} self-start">${order.Status}</span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><strong>Date:</strong> ${new Date(order.PlacedAt).toLocaleDateString()}</p>
                            <p><strong>Items:</strong> ${itemsText}</p>
                            <p><strong>Total:</strong> <span class="text-red-600 font-semibold">$${order.OrderTotal}</span></p>
                        </div>
                    </div>
                    <div class="flex items-center text-red-500">
                        <span class="text-sm font-medium mr-2">View Details</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function filterOrders(status) {
    if (status === '') {
        filteredOrders = [...currentOrders];
    } else {
        filteredOrders = currentOrders.filter(order => order.Status === status);
    }
    displayOrders(filteredOrders);
}

function searchOrders(searchTerm) {
    if (searchTerm === '') {
        filteredOrders = [...currentOrders];
    } else {
        filteredOrders = currentOrders.filter(order => {
            return order.OrderID.toString().includes(searchTerm) ||
                   order.Status.toLowerCase().includes(searchTerm) ||
                   order.items.some(item => item.ProductName.toLowerCase().includes(searchTerm));
        });
    }
    displayOrders(filteredOrders);
}

function showOrderDetails(orderId) {
    const order = currentOrders.find(o => o.OrderID == orderId);
    if (!order) return;

    const statusClasses = {
        'processing': 'bg-yellow-100 text-yellow-800',
        'shipped': 'bg-blue-100 text-blue-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    };

    const statusClass = statusClasses[order.Status] || 'bg-gray-100 text-gray-800';

    const modalContent = document.getElementById('order-modal-content');
    modalContent.innerHTML = `
        <div class="space-y-6">
            <!-- Order Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Order #${order.OrderID}</h3>
                    <p class="text-gray-600">Placed on ${new Date(order.PlacedAt).toLocaleDateString()}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass} self-start">${order.Status}</span>
            </div>

            <!-- Order Items -->
            <div>
                <h4 class="font-semibold text-gray-900 mb-3">Order Items</h4>
                <div class="space-y-3">
                    ${order.items.map(item => `
                        <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-b-0">
                            <div>
                                <p class="font-medium text-gray-900">${item.ProductName}</p>
                                <p class="text-sm text-gray-600">Quantity: ${item.Quantity}</p>
                                <p class="text-sm text-gray-600">Unit Price: $${item.UnitPriceAtOrder}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">$${(item.Quantity * item.UnitPriceAtOrder).toFixed(2)}</p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center text-lg font-semibold">
                    <span>Order Total:</span>
                    <span class="text-red-600">$${order.OrderTotal}</span>
                </div>
                <p class="text-sm text-gray-600 mt-2">Payment Method: Cash on Delivery</p>
            </div>

            <!-- Order Status Timeline -->
            <div>
                <h4 class="font-semibold text-gray-900 mb-3">Order Status</h4>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full ${order.Status === 'processing' || order.Status === 'shipped' || order.Status === 'delivered' ? 'bg-green-500' : 'bg-gray-300'} mr-3"></div>
                        <span class="text-sm">Order Placed</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full ${order.Status === 'shipped' || order.Status === 'delivered' ? 'bg-green-500' : 'bg-gray-300'} mr-3"></div>
                        <span class="text-sm">Order Shipped</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full ${order.Status === 'delivered' ? 'bg-green-500' : 'bg-gray-300'} mr-3"></div>
                        <span class="text-sm">Order Delivered</span>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('order-modal').classList.remove('hidden');
}

function closeOrderModal() {
    document.getElementById('order-modal').classList.add('hidden');
}

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