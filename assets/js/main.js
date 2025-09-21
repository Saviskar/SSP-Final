// Main JavaScript file for Pet Haven ecommerce
class PetHaven {
    constructor() {
        this.cart = [];
        this.currentUser = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.checkAuthStatus();
        this.loadCartCount();
    }

    bindEvents() {
        // Registration form
        const registerForm = document.querySelector('#register-form');
        if (registerForm) {
            registerForm.addEventListener('submit', this.handleRegistration.bind(this));
        }

        // Login form
        const loginForm = document.querySelector('#login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', this.handleLogin.bind(this));
        }

        // Add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart-btn')) {
                this.handleAddToCart(e);
            }
        });

        // Cart quantity controls
        document.addEventListener('click', (e) => {
            if (e.target.matches('.quantity-btn')) {
                this.handleQuantityChange(e);
            }
        });

        // Remove from cart
        document.addEventListener('click', (e) => {
            if (e.target.matches('.remove-btn')) {
                this.handleRemoveFromCart(e);
            }
        });

        // Product search
        const searchInput = document.querySelector('#product-search');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 300));
        }

        // Category filters
        document.addEventListener('click', (e) => {
            if (e.target.matches('.category-filter')) {
                this.handleCategoryFilter(e);
            }
        });
    }

    // Authentication Methods
    async handleRegistration(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('../process/register.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage('Registration successful! You can now log in.', 'success');
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Registration failed. Please try again.', 'error');
            console.error('Registration error:', error);
        }
    }

    async handleLogin(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('../process/login.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage('Login successful!', 'success');
                setTimeout(() => {
                    window.location.href = result.redirect || 'landing.html';
                }, 1000);
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Login failed. Please try again.', 'error');
            console.error('Login error:', error);
        }
    }

    async checkAuthStatus() {
        try {
            const response = await fetch('../process/auth_check.php');
            const result = await response.json();
            
            if (result.success) {
                this.currentUser = result.data;
                this.updateAuthUI(true);
            } else {
                this.updateAuthUI(false);
            }
        } catch (error) {
            console.error('Auth check error:', error);
            this.updateAuthUI(false);
        }
    }

    updateAuthUI(isLoggedIn) {
        const authElements = document.querySelectorAll('.auth-required');
        const guestElements = document.querySelectorAll('.guest-only');
        
        authElements.forEach(el => {
            el.style.display = isLoggedIn ? 'block' : 'none';
        });
        
        guestElements.forEach(el => {
            el.style.display = isLoggedIn ? 'none' : 'block';
        });
        
        if (isLoggedIn && this.currentUser) {
            const userNameElements = document.querySelectorAll('.user-name');
            userNameElements.forEach(el => {
                el.textContent = this.currentUser.name;
            });
        }
    }

    // Product Management
    async loadProducts(filters = {}) {
        try {
            const params = new URLSearchParams(filters);
            const response = await fetch(`../process/products.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayProducts(result.data.products);
                this.displayCategories(result.data.categories);
            } else {
                this.showMessage('Failed to load products', 'error');
            }
        } catch (error) {
            this.showMessage('Failed to load products', 'error');
            console.error('Load products error:', error);
        }
    }

    displayProducts(products) {
    const container = document.querySelector('#products-container');
    if (!container) return;

    container.innerHTML = products.map(product => `
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
            <a href="product.html?id=${product.ProductID}" class="block group">
                <div class="relative h-48 bg-gray-100">
                    ${product.ImageURL
                        ? `<img src="${product.ImageURL}" alt="${product.ProductName}" class="w-full h-full object-cover">`
                        : `<div class="w-16 h-16 bg-gray-400 rounded-full"></div>`
                    }
                    ${product.DiscountPercentage > 0
                        ? `<span class="absolute top-2 right-2 bg-red-600 text-white px-2 py-0.5 rounded text-xs font-bold">${product.DiscountPercentage}% OFF</span>`
                        : ``
                    }
                </div>
                <div class="p-4">
                    <h3 class="font-medium text-gray-900 mb-2 group-hover:underline">
                        ${product.ProductName}
                    </h3>
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            ${product.DiscountPercentage > 0
                                ? `<span class="text-sm text-gray-500 line-through">$${product.Price}</span>
                                   <span class="text-lg font-semibold text-red-600">$${product.DiscountedPrice}</span>`
                                : `<span class="text-lg font-semibold text-gray-900">$${product.Price}</span>`
                            }
                        </div>
                        <button
                            class="add-to-cart-btn bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition-colors text-sm"
                            data-product-id="${product.ProductID}"
                            data-product-name="${product.ProductName}"
                            data-product-price="${product.DiscountedPrice || product.Price}"
                            ${product.Stock <= 0 ? 'disabled' : ''}
                        >
                            ${product.Stock <= 0 ? 'Out of Stock' : 'Add to Cart'}
                        </button>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Stock: ${product.Stock} | ${product.CategoryName}
                    </div>
                </div>
            </a>
        </div>
    `).join('');
    }


    displayCategories(categories) {
        const container = document.querySelector('#categories-container');
        if (!container) return;
        
        container.innerHTML = categories.map(category => `
            <button class="category-filter px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-full text-sm font-medium flex-shrink-0" 
                    data-category="${category.CategoryName}">
                ${category.CategoryName}
            </button>
        `).join('');
    }

    async handleSearch(e) {
        const searchTerm = e.target.value.trim();
        await this.loadProducts({ search: searchTerm });
    }

    handleCategoryFilter(e) {
        const category = e.target.dataset.category;
        
        // Update active filter UI
        document.querySelectorAll('.category-filter').forEach(btn => {
            btn.classList.remove('bg-red-100', 'text-red-600');
            btn.classList.add('bg-gray-100', 'text-gray-600');
        });
        
        e.target.classList.remove('bg-gray-100', 'text-gray-600');
        e.target.classList.add('bg-red-100', 'text-red-600');
        
        this.loadProducts({ category });
    }

    // Cart Management
    async handleAddToCart(e) {
        if (!this.currentUser) {
            this.showMessage('Please log in to add items to cart', 'error');
            return;
        }
        
        const productId = e.target.dataset.productId;
        const quantity = 1;
        
        const formData = new FormData();
        formData.append('productId', productId);
        formData.append('quantity', quantity);
        
        try {
            const response = await fetch('../process/cart.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage(result.message, 'success');
                this.loadCartCount();
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Failed to add item to cart', 'error');
            console.error('Add to cart error:', error);
        }
    }

    async loadCart() {
        try {
            const response = await fetch('../process/cart.php');
            const result = await response.json();
            
            if (result.success) {
                this.displayCart(result.data);
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Failed to load cart', 'error');
            console.error('Load cart error:', error);
        }
    }

    async loadCartCount() {
        try {
            const response = await fetch('../process/cart.php');
            const result = await response.json();
            
            if (result.success) {
                const totalItems = result.data.summary.totalItems;
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(el => {
                    el.textContent = totalItems;
                    el.style.display = totalItems > 0 ? 'block' : 'none';
                });
            }
        } catch (error) {
            console.error('Load cart count error:', error);
        }
    }

    displayCart(cartData) {
        const container = document.querySelector('#cart-items-container');
        const summaryContainer = document.querySelector('#cart-summary');
        
        if (!container) return;
        
        if (cartData.items.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500 py-8">Your cart is empty</p>';
            return;
        }
        
        container.innerHTML = cartData.items.map(item => `
            <div class="cart-item p-4 border-b border-gray-200" data-item-id="${item.CartItemID}">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0">
                        ${item.ImageURL ? 
                            `<img src="${item.ImageURL}" alt="${item.ProductName}" class="w-full h-full object-cover rounded-lg">` :
                            `<div class="w-full h-full bg-gray-300 rounded-lg"></div>`
                        }
                    </div>
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900">${item.ProductName}</h3>
                        <p class="text-sm text-gray-500">Stock: ${item.Stock}</p>
                        ${item.DiscountPercentage > 0 ? 
                            `<div class="flex items-center space-x-2">
                                <span class="text-sm line-through text-gray-400">$${item.Price}</span>
                                <span class="text-sm font-medium text-red-600">$${item.DiscountedPrice} (${item.DiscountPercentage}% off)</span>
                            </div>` :
                            `<span class="text-sm font-medium">$${item.Price}</span>`
                        }
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="quantity-btn w-8 h-8 border border-gray-300 rounded hover:bg-gray-50" 
                                data-action="decrease" data-item-id="${item.CartItemID}">-</button>
                        <span class="w-8 text-center">${item.Quantity}</span>
                        <button class="quantity-btn w-8 h-8 border border-gray-300 rounded hover:bg-gray-50" 
                                data-action="increase" data-item-id="${item.CartItemID}">+</button>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold">$${item.DiscountedItemTotal}</div>
                        <button class="remove-btn text-red-500 hover:text-red-700 text-sm" 
                                data-item-id="${item.CartItemID}">Remove</button>
                    </div>
                </div>
            </div>
        `).join('');
        
        if (summaryContainer) {
            summaryContainer.innerHTML = `
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>$${cartData.summary.subtotal}</span>
                        </div>
                        ${cartData.summary.savings > 0 ? 
                            `<div class="flex justify-between text-green-600">
                                <span>Savings:</span>
                                <span>-$${cartData.summary.savings}</span>
                            </div>` : ''
                        }
                        <div class="flex justify-between">
                            <span>Shipping:</span>
                            <span>$${cartData.summary.shipping}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t pt-2">
                            <span>Total:</span>
                            <span>$${cartData.summary.total}</span>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    async handleQuantityChange(e) {
        const action = e.target.dataset.action;
        const itemId = parseInt(e.target.dataset.itemId);
        const currentQuantityEl = e.target.parentNode.querySelector('span');
        let currentQuantity = parseInt(currentQuantityEl.textContent);
        
        if (action === 'increase') {
            currentQuantity++;
        } else if (action === 'decrease' && currentQuantity > 1) {
            currentQuantity--;
        } else if (action === 'decrease' && currentQuantity === 1) {
            // Remove item if quantity becomes 0
            await this.handleRemoveFromCart(e);
            return;
        }
        
        await this.updateCartItemQuantity(itemId, currentQuantity);
    }

    async updateCartItemQuantity(itemId, quantity) {
        try {
            const response = await fetch('../process/cart.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cartItemId: itemId,
                    quantity: quantity
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.loadCart(); // Reload cart to update totals
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Failed to update cart', 'error');
            console.error('Update cart error:', error);
        }
    }

    async handleRemoveFromCart(e) {
        const itemId = parseInt(e.target.dataset.itemId);
        
        try {
            const response = await fetch('../process/cart.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cartItemId: itemId
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage(result.message, 'success');
                this.loadCart();
                this.loadCartCount();
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Failed to remove item', 'error');
            console.error('Remove from cart error:', error);
        }
    }

    // Order Management
    async placeOrder(orderData) {
        try {
            const formData = new FormData();
            Object.keys(orderData).forEach(key => {
                formData.append(key, orderData[key]);
            });
            
            const response = await fetch('../process/orders.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage('Order placed successfully!', 'success');
                setTimeout(() => {
                    window.location.href = 'order-confirmation.html?orderId=' + result.data.orderId;
                }, 2000);
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Failed to place order', 'error');
            console.error('Place order error:', error);
        }
    }

    // Utility Methods
    showMessage(message, type = 'info') {
        // Create or update message element
        let messageEl = document.querySelector('#message-container');
        
        if (!messageEl) {
            messageEl = document.createElement('div');
            messageEl.id = 'message-container';
            messageEl.className = 'fixed top-4 right-4 z-50 max-w-sm';
            document.body.appendChild(messageEl);
        }
        
        const bgColor = type === 'success' ? 'bg-green-500' : 
                       type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        
        messageEl.innerHTML = `
            <div class="${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-0">
                ${message}
            </div>
        `;
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (messageEl && messageEl.parentNode) {
                messageEl.style.opacity = '0';
                messageEl.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (messageEl && messageEl.parentNode) {
                        messageEl.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize the application
document.addEventListener('DOMContentLoaded', () => {
    window.petHaven = new PetHaven();
});

// Additional utility functions for specific pages

// Add Product Form Handler (Admin)
function handleAddProduct(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    fetch('../process/products.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            window.petHaven.showMessage(result.message, 'success');
            e.target.reset();
            // Reload products list if on admin page
            if (typeof loadAdminProducts === 'function') {
                loadAdminProducts();
            }
        } else {
            window.petHaven.showMessage(result.message, 'error');
        }
    })
    .catch(error => {
        window.petHaven.showMessage('Failed to add product', 'error');
        console.error('Add product error:', error);
    });
}

// Load Admin Products
async function loadAdminProducts() {
    try {
        const response = await fetch('../process/products.php');
        const result = await response.json();
        
        if (result.success) {
            displayAdminProducts(result.data.products);
        } else {
            window.petHaven.showMessage('Failed to load products', 'error');
        }
    } catch (error) {
        window.petHaven.showMessage('Failed to load products', 'error');
        console.error('Load admin products error:', error);
    }
}

function displayAdminProducts(products) {
    const container = document.querySelector('#admin-products-container');
    if (!container) return;
    
    container.innerHTML = products.map(product => `
        <div class="grid grid-cols-6 gap-4 px-6 py-4 border-b border-gray-200 items-center">
            <div class="text-gray-900 text-sm">${product.ProductID}</div>
            <div class="text-red-400 text-sm">${product.ProductName}</div>
            <div class="text-red-400 text-sm">${product.Stock}</div>
            <div class="text-red-400 text-sm">${product.Price}</div>
            <div class="text-red-400 text-sm cursor-pointer hover:underline" onclick="editProduct(${product.ProductID})">Edit</div>
            <div class="flex justify-start">
                <button class="w-6 h-6 bg-red-500 flex items-center justify-center rounded hover:bg-red-600 transition-colors" onclick="deleteProduct(${product.ProductID})">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
}

// Checkout Form Handler
function handleCheckout(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const orderData = Object.fromEntries(formData);
    
    window.petHaven.placeOrder(orderData);
}

// Load Orders (Admin and User)
async function loadOrders() {
    try {
        const response = await fetch('../process/orders.php');
        const result = await response.json();
        
        if (result.success) {
            displayOrders(result.data);
        } else {
            window.petHaven.showMessage(result.message, 'error');
        }
    } catch (error) {
        window.petHaven.showMessage('Failed to load orders', 'error');
        console.error('Load orders error:', error);
    }
}

function displayOrders(orders) {
    const container = document.querySelector('#orders-container');
    if (!container) return;
    
    if (orders.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-8">No orders found</p>';
        return;
    }
    
    container.innerHTML = orders.map(order => {
        const statusClass = {
            'processing': 'bg-yellow-100 text-yellow-800',
            'shipped': 'bg-blue-100 text-blue-800',
            'delivered': 'bg-green-100 text-green-800',
            'cancelled': 'bg-red-100 text-red-800'
        };
        
        const itemsText = order.items.map(item => 
            `${item.ProductID} x ${item.Quantity}`
        ).join(', ');
        
        return `
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200 mb-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Order #${order.OrderID}</div>
                        ${order.CustomerName ? `<div class="text-red-600 text-sm">${order.CustomerName}</div>` : ''}
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass[order.Status] || 'bg-gray-100 text-gray-800'}">${order.Status}</span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="text-gray-900">${new Date(order.PlacedAt).toLocaleDateString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Products:</span>
                        <span class="text-gray-900">${itemsText}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total:</span>
                        <span class="text-red-600 font-medium">${order.OrderTotal}</span>
                    </div>
                    ${order.AddressLine ? `
                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-gray-600 text-xs">Delivery Address:</span>
                        <div class="text-red-600 text-sm mt-1">${order.AddressLine}${order.CityName ? ', ' + order.CityName : ''}</div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

// Promotions Management
async function loadPromotions() {
    try {
        const response = await fetch('../process/promotions.php');
        const result = await response.json();
        
        if (result.success) {
            displayPromotions(result.data.promotions);
        } else {
            window.petHaven.showMessage('Failed to load promotions', 'error');
        }
    } catch (error) {
        window.petHaven.showMessage('Failed to load promotions', 'error');
        console.error('Load promotions error:', error);
    }
}

function displayPromotions(promotions) {
    const container = document.querySelector('#promotions-container');
    if (!container) return;
    
    container.innerHTML = promotions.map(promo => `
        <div class="grid grid-cols-3 gap-4 px-6 py-4 border-b border-gray-200 items-center">
            <div class="text-gray-900 text-sm">${promo.ProductID}</div>
            <div class="text-rose-600 text-sm">${promo.PromotionPercentage}%</div>
            <div class="flex items-center gap-3">
                <button class="text-rose-600 hover:text-rose-700 text-sm font-medium transition-colors" onclick="editPromotion(${promo.ProductID}, ${promo.PromotionPercentage})">
                    Edit
                </button>
                <button class="text-red-500 hover:text-red-700 transition-colors" onclick="deletePromotion(${promo.ProductID})">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
}

async function deletePromotion(productId) {
    if (!confirm('Are you sure you want to delete this promotion?')) return;
    
    try {
        const response = await fetch('../process/promotions.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ productId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.petHaven.showMessage(result.message, 'success');
            loadPromotions();
        } else {
            window.petHaven.showMessage(result.message, 'error');
        }
    } catch (error) {
        window.petHaven.showMessage('Failed to delete promotion', 'error');
        console.error('Delete promotion error:', error);
    }
}

// Mobile menu toggle (safe)
const mobileBtn = document.getElementById('mobile-menu-button');
if (mobileBtn) {
  mobileBtn.addEventListener('click', function () {
    const menu = document.getElementById('mobile-menu');
    if (menu) menu.classList.toggle('hidden');
  });
}


// Logout
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