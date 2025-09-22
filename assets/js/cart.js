// Load cart when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCartPage();
});

async function loadCartPage() {
    const loadingEl = document.getElementById('cart-loading');
    const emptyCartEl = document.getElementById('empty-cart');
    const cartItemsEl = document.getElementById('cart-items');
    
    try {
        loadingEl.classList.remove('hidden');
        emptyCartEl.classList.add('hidden');
        cartItemsEl.classList.add('hidden');
        
        if (!window.petHaven.currentUser) {
            // Check auth status first
            await window.petHaven.checkAuthStatus();
        }
        
        if (!window.petHaven.currentUser) {
            // Redirect to login if not authenticated
            window.location.href = 'login.php';
            return;
        }
        
        await window.petHaven.loadCart();
        
        // Check if cart is empty after loading
        const cartContainer = document.getElementById('cart-items-container');
        if (!cartContainer.children.length) {
            loadingEl.classList.add('hidden');
            emptyCartEl.classList.remove('hidden');
        } else {
            loadingEl.classList.add('hidden');
            cartItemsEl.classList.remove('hidden');
        }
        
    } catch (error) {
        console.error('Error loading cart:', error);
        loadingEl.classList.add('hidden');
        emptyCartEl.classList.remove('hidden');
        window.petHaven.showMessage('Failed to load cart', 'error');
    }
}

// Clear cart functionality
document.getElementById('clear-cart').addEventListener('click', async function() {
    if (!confirm('Are you sure you want to clear your cart? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('../process/cart.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ clearAll: true })
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.petHaven.showMessage('Cart cleared successfully', 'success');
            loadCartPage(); // Reload the cart page
        } else {
            window.petHaven.showMessage(result.message, 'error');
        }
    } catch (error) {
        window.petHaven.showMessage('Failed to clear cart', 'error');
        console.error('Clear cart error:', error);
    }
});

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
                window.location.href = 'landing.html';
            }, 1000);
        }
    } catch (error) {
        console.error('Logout error:', error);
    }
}