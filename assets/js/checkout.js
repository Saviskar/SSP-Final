let cartData = null;

// Load checkout data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCheckoutData();
    prefillUserData();
});

async function loadCheckoutData() {
    const orderLoading = document.getElementById('order-loading');
    const orderItems = document.getElementById('order-items');
    const orderTotals = document.getElementById('order-totals');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const placeOrderText = document.getElementById('place-order-text');
    
    try {
        // Check authentication first
        if (!window.petHaven.currentUser) {
            await window.petHaven.checkAuthStatus();
        }
        
        if (!window.petHaven.currentUser) {
            window.location.href = 'login.html';
            return;
        }
        
        // Load cart data
        const response = await fetch('../process/cart.php');
        const result = await response.json();
        
        if (result.success) {
            cartData = result.data;
            
            if (cartData.items.length === 0) {
                window.petHaven.showMessage('Your cart is empty', 'error');
                setTimeout(() => {
                    window.location.href = 'cart.html';
                }, 2000);
                return;
            }
            
            displayOrderSummary(cartData);
            orderLoading.classList.add('hidden');
            orderItems.classList.remove('hidden');
            orderTotals.classList.remove('hidden');
            placeOrderBtn.disabled = false;
            placeOrderText.textContent = 'Place Order';
            
        } else {
            throw new Error(result.message);
        }
        
    } catch (error) {
        console.error('Error loading checkout data:', error);
        window.petHaven.showMessage('Failed to load order details', 'error');
        orderLoading.classList.add('hidden');
        placeOrderText.textContent = 'Error - Cannot Place Order';
    }
}

function displayOrderSummary(cartData) {
    const orderItems = document.getElementById('order-items');
    const orderTotals = document.getElementById('order-totals');
    
    // Display items
    orderItems.innerHTML = cartData.items.map(item => `
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0">
                ${item.ImageURL ? 
                    `<img src="${item.ImageURL}" alt="${item.ProductName}" class="w-full h-full object-cover rounded-lg">` :
                    `<div class="w-full h-full bg-gray-300 rounded-lg"></div>`
                }
            </div>
            <div class="flex-1">
                <h3 class="font-medium text-gray-900">${item.ProductName}</h3>
                <p class="text-sm text-gray-500">Quantity: ${item.Quantity}</p>
                ${item.DiscountPercentage > 0 ? 
                    `<div class="flex items-center space-x-2">
                        <span class="text-sm line-through text-gray-400">$${item.Price}</span>
                        <span class="text-sm font-medium text-red-600">$${item.DiscountedPrice}</span>
                    </div>` :
                    `<span class="text-sm font-medium">$${item.Price}</span>`
                }
            </div>
            <div class="text-right">
                <p class="font-medium text-gray-900">$${item.DiscountedItemTotal}</p>
            </div>
        </div>
    `).join('');
    
    // Display totals
    orderTotals.innerHTML = `
        <div class="flex justify-between">
            <span class="text-gray-600">Subtotal</span>
            <span class="font-medium text-gray-900">$${cartData.summary.discountedSubtotal}</span>
        </div>
        ${cartData.summary.savings > 0 ? 
            `<div class="flex justify-between text-green-600">
                <span>You Save</span>
                <span>-$${cartData.summary.savings}</span>
            </div>` : ''
        }
        <div class="flex justify-between">
            <span class="text-gray-600">Shipping</span>
            <span class="font-medium text-gray-900">$${cartData.summary.shipping}</span>
        </div>
        <div class="flex justify-between text-lg font-semibold border-t border-gray-200 pt-3">
            <span class="text-gray-900">Total</span>
            <span class="text-gray-900">$${cartData.summary.total}</span>
        </div>
    `;
}

async function prefillUserData() {
    if (window.petHaven.currentUser) {
        const emailField = document.getElementById('email');
        const fullNameField = document.getElementById('fullName');
        
        emailField.value = window.petHaven.currentUser.email || '';
        fullNameField.value = window.petHaven.currentUser.name || '';
    }
}

// Handle checkout form submission
document.getElementById('checkout-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('place-order-btn');
    const submitText = document.getElementById('place-order-text');
    const originalText = submitText.textContent;
    
    submitBtn.disabled = true;
    submitText.textContent = 'Placing Order...';
    
    try {
        const formData = new FormData(e.target);
        const response = await fetch('../process/orders.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.petHaven.showMessage('Order placed successfully!', 'success');
            
            // Redirect to a success page or order confirmation
            setTimeout(() => {
                window.location.href = `order-success.php?orderId=${result.data.orderId}`;
            }, 2000);
        } else {
            window.petHaven.showMessage(result.message, 'error');
            submitBtn.disabled = false;
            submitText.textContent = originalText;
        }
        
    } catch (error) {
        console.error('Checkout error:', error);
        window.petHaven.showMessage('Failed to place order. Please try again.', 'error');
        submitBtn.disabled = false;
        submitText.textContent = originalText;
    }
});