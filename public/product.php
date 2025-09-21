<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <nav class="flex text-sm overflow-x-auto whitespace-nowrap scrollbar-hide" id="breadcrumb">
        <a href="landing.php" class="text-red-500 hover:text-red-600 flex-shrink-0">Home</a>
        <span class="mx-2 text-gray-500 flex-shrink-0">/</span>
        <a href="allproducts.php" class="text-red-500 hover:text-red-600 flex-shrink-0">Shop</a>
        <span class="mx-2 text-gray-500 flex-shrink-0">/</span>
        <span class="text-gray-900 flex-shrink-0" id="product-breadcrumb">Product</span>
    </nav>
</div>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Loading State -->
    <div id="product-loading" class="flex items-center justify-center py-12">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
            <p class="text-gray-500">Loading product details...</p>
        </div>
    </div>

    <!-- Product Not Found -->
    <div id="product-not-found" class="hidden text-center py-12">
        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Product not found</h2>
        <p class="text-gray-500 mb-6">The product you're looking for doesn't exist or has been removed.</p>
        <a href="allproducts.php" class="inline-flex items-center px-6 py-3 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Shop
        </a>
    </div>

    <!-- Product Details -->
    <div id="product-details" class="hidden grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        <!-- Product Image -->
        <div class="bg-green-100 rounded-2xl p-8 lg:p-12 flex flex-col justify-center items-center min-h-96 relative">
            <div id="product-image-container" class="flex items-center justify-center w-full max-w-md">
                <!-- Default rope toy illustration -->
                <div class="flex items-center justify-center w-full max-w-md">
                    <!-- Left Tassel -->
                    <div class="relative">
                        <div class="w-16 h-20 bg-gray-600 rounded-full transform -rotate-12 opacity-80"></div>
                        <div class="absolute top-2 left-2 w-12 h-16 bg-gray-700 rounded-full transform -rotate-12"></div>
                        <div class="absolute top-4 left-4 w-8 h-12 bg-gray-800 rounded-full transform -rotate-12"></div>
                    </div>
                    
                    <!-- Rope Body -->
                    <div class="flex-1 mx-4">
                        <div class="h-6 bg-gray-300 rounded-full mb-2 shadow-inner"></div>
                        <div class="h-4 bg-gray-500 rounded-full mb-2"></div>
                        <div class="h-4 bg-gray-600 rounded-full mb-2"></div>
                        <div class="h-6 bg-gray-400 rounded-full shadow-inner"></div>
                    </div>
                    
                    <!-- Right Tassel -->
                    <div class="relative">
                        <div class="w-16 h-20 bg-gray-600 rounded-full transform rotate-12 opacity-80"></div>
                        <div class="absolute top-2 right-2 w-12 h-16 bg-gray-700 rounded-full transform rotate-12"></div>
                        <div class="absolute top-4 right-4 w-8 h-12 bg-gray-800 rounded-full transform rotate-12"></div>
                    </div>
                </div>
            </div>
            
            <!-- Watermark -->
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 text-gray-600 text-sm tracking-widest opacity-70">
                NINUNALD NAGLTA LEFE GLE WORK
            </div>
        </div>

        <!-- Product Details -->
        <div class="flex flex-col justify-start space-y-6">
            <h1 id="product-title" class="text-3xl lg:text-4xl font-bold text-gray-800">Loading...</h1>
            
            <!-- Pricing -->
            <div class="flex items-center space-x-4" id="product-pricing">
                <span id="original-price" class="text-xl text-gray-500 line-through hidden">$0.00</span>
                <span id="current-price" class="text-3xl font-bold text-red-500">$0.00</span>
                <span id="discount-badge" class="hidden bg-red-500 text-white px-2 py-1 rounded-full text-sm font-bold">0% OFF</span>
            </div>
            
            <!-- Stock Status -->
            <div id="stock-status" class="text-sm font-medium">
                <span id="stock-text" class="text-gray-600">Checking availability...</span>
            </div>
            
            <!-- Description -->
            <div id="product-description" class="text-gray-600 text-lg leading-relaxed">
                Loading product description...
            </div>
            
            <!-- Quantity Section -->
            <div class="space-y-4">
                <label class="block text-gray-800 font-semibold">Quantity</label>
                <div class="flex items-center space-x-4">
                    <button id="decrease-btn" class="w-10 h-10 border-2 border-gray-300 rounded-lg hover:border-red-500 hover:text-red-500 transition-colors flex items-center justify-center font-bold" disabled>
                        -
                    </button>
                    <input id="quantity-input" type="number" value="1" min="1" max="1" class="w-16 text-center border-2 border-gray-300 rounded-lg py-2 focus:border-red-500 outline-none" disabled>
                    <button id="increase-btn" class="w-10 h-10 border-2 border-gray-300 rounded-lg hover:border-red-500 hover:text-red-500 transition-colors flex items-center justify-center font-bold" disabled>
                        +
                    </button>
                    <span id="total-price" class="text-xl font-bold text-gray-800 ml-4">$0.00</span>
                </div>
            </div>
            
            <!-- Add to Cart Button -->
            <button id="add-to-cart-btn" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-lg transition-colors text-lg" disabled>
                <span id="add-to-cart-text">Loading...</span>
            </button>

            <!-- Additional Product Info -->
            <div id="product-info" class="space-y-2 text-sm text-gray-600 border-t border-gray-200 pt-4">
                <p><strong>Category:</strong> <span id="product-category">-</span></p>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="bg-pink-50 mt-16 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Footer Links -->
        <div class="flex flex-wrap justify-center gap-8 mb-8">
            <a href="#" class="text-gray-600 hover:text-red-500 font-medium transition-colors">About</a>
            <a href="#" class="text-gray-600 hover:text-red-500 font-medium transition-colors">Contact</a>
            <a href="#" class="text-gray-600 hover:text-red-500 font-medium transition-colors">FAQ</a>
            <a href="#" class="text-gray-600 hover:text-red-500 font-medium transition-colors">Shipping & Returns</a>
            <a href="#" class="text-gray-600 hover:text-red-500 font-medium transition-colors">Privacy Policy</a>
        </div>

        <!-- Social Links -->
        <div class="flex justify-center space-x-6 mb-8">
            <a href="#" class="text-gray-600 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" />
                </svg>
            </a>
            <a href="#" class="text-gray-600 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                    <path d="m16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                </svg>
            </a>
            <a href="#" class="text-gray-600 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            </a>
        </div>

        <!-- Copyright -->
        <div class="text-center text-gray-600">
            <p>©2024 Pet Haven. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="../assets/js/main.js"></script>
<script>
let currentProduct = null;
let productId = null;

// Get product ID from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    productId = urlParams.get('id');
    
    if (productId) {
        loadProduct(productId);
    } else {
        showProductNotFound();
    }
    
    setupEventListeners();
});

function setupEventListeners() {
    // Quantity controls
    document.getElementById('decrease-btn').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantity-input');
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
            quantity--;
            quantityInput.value = quantity;
            updateTotalPrice();
        }
    });

    document.getElementById('increase-btn').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantity-input');
        let quantity = parseInt(quantityInput.value);
        const maxQuantity = parseInt(quantityInput.max);
        if (quantity < maxQuantity) {
            quantity++;
            quantityInput.value = quantity;
            updateTotalPrice();
        }
    });

    document.getElementById('quantity-input').addEventListener('change', function() {
        const quantity = parseInt(this.value);
        const maxQuantity = parseInt(this.max);
        
        if (quantity < 1) {
            this.value = 1;
        } else if (quantity > maxQuantity) {
            this.value = maxQuantity;
        }
        updateTotalPrice();
    });

    // Add to cart button
    document.getElementById('add-to-cart-btn').addEventListener('click', handleAddToCart);

    // Mobile menu
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
}

async function loadProduct(productId) {
    const loadingEl = document.getElementById('product-loading');
    const notFoundEl = document.getElementById('product-not-found');
    const detailsEl = document.getElementById('product-details');

    try {
        loadingEl.classList.remove('hidden');
        notFoundEl.classList.add('hidden');
        detailsEl.classList.add('hidden');

        const response = await fetch(`../process/products.php?id=${productId}`);
        const result = await response.json();

        loadingEl.classList.add('hidden');

        if (result.success && result.data.products.length > 0) {
            currentProduct = result.data.products[0];
            displayProduct(currentProduct);
            detailsEl.classList.remove('hidden');
        } else {
            showProductNotFound();
        }

    } catch (error) {
        console.error('Error loading product:', error);
        loadingEl.classList.add('hidden');
        showProductNotFound();
    }
}

function displayProduct(product) {
    // Update title and breadcrumb
    document.getElementById('product-title').textContent = product.ProductName;
    document.getElementById('product-breadcrumb').textContent = product.ProductName;
    document.title = `Pet Haven - ${product.ProductName}`;

    // Update pricing
    const originalPriceEl = document.getElementById('original-price');
    const currentPriceEl = document.getElementById('current-price');
    const discountBadgeEl = document.getElementById('discount-badge');

    if (product.DiscountPercentage > 0) {
        originalPriceEl.textContent = `$${product.Price}`;
        originalPriceEl.classList.remove('hidden');
        currentPriceEl.textContent = `$${product.DiscountedPrice}`;
        discountBadgeEl.textContent = `${product.DiscountPercentage}% OFF`;
        discountBadgeEl.classList.remove('hidden');
    } else {
        currentPriceEl.textContent = `$${product.Price}`;
        originalPriceEl.classList.add('hidden');
        discountBadgeEl.classList.add('hidden');
    }

    // Update stock status
    const stockTextEl = document.getElementById('stock-text');
    if (product.Stock <= 0) {
        stockTextEl.textContent = 'Out of Stock';
        stockTextEl.className = 'text-red-600 font-semibold';
    } else if (product.Stock < 10) {
        stockTextEl.textContent = `Only ${product.Stock} left in stock`;
        stockTextEl.className = 'text-yellow-600 font-semibold';
    } else {
        stockTextEl.textContent = 'In Stock';
        stockTextEl.className = 'text-green-600 font-semibold';
    }

    // Update description
    document.getElementById('product-description').textContent = 
        product.Description || 'This product provides excellent quality and value for your pet.';

    // Update quantity controls
    const quantityInput = document.getElementById('quantity-input');
    const decreaseBtn = document.getElementById('decrease-btn');
    const increaseBtn = document.getElementById('increase-btn');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const addToCartText = document.getElementById('add-to-cart-text');

    if (product.Stock > 0) {
        quantityInput.max = product.Stock;
        quantityInput.disabled = false;
        decreaseBtn.disabled = false;
        increaseBtn.disabled = false;
        addToCartBtn.disabled = false;
        addToCartText.textContent = 'Add to Cart';
        addToCartBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
        addToCartBtn.classList.add('bg-red-500', 'hover:bg-red-600');
    } else {
        quantityInput.disabled = true;
        decreaseBtn.disabled = true;
        increaseBtn.disabled = true;
        addToCartBtn.disabled = true;
        addToCartText.textContent = 'Out of Stock';
        addToCartBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        addToCartBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
    }

    // Update additional info
    document.getElementById('product-category').textContent = product.CategoryName;

    // Update image if available
    if (product.ImageURL) {
        const imageContainer = document.getElementById('product-image-container');
        imageContainer.innerHTML = `
            <img src="${product.ImageURL}" alt="${product.ProductName}" 
                 class="w-full h-full object-cover rounded-lg max-w-md max-h-80"
                 onerror="this.parentNode.innerHTML = getDefaultProductIllustration();">
        `;
    }

    // Update initial total price
    updateTotalPrice();
}

function updateTotalPrice() {
    if (!currentProduct) return;
    
    const quantity = parseInt(document.getElementById('quantity-input').value);
    const unitPrice = currentProduct.DiscountedPrice || currentProduct.Price;
    const totalPrice = (quantity * unitPrice).toFixed(2);
    
    document.getElementById('total-price').textContent = `$${totalPrice}`;
}

function getDefaultProductIllustration() {
    return `
        <div class="flex items-center justify-center w-full max-w-md">
            <!-- Left Tassel -->
            <div class="relative">
                <div class="w-16 h-20 bg-gray-600 rounded-full transform -rotate-12 opacity-80"></div>
                <div class="absolute top-2 left-2 w-12 h-16 bg-gray-700 rounded-full transform -rotate-12"></div>
                <div class="absolute top-4 left-4 w-8 h-12 bg-gray-800 rounded-full transform -rotate-12"></div>
            </div>
            
            <!-- Rope Body -->
            <div class="flex-1 mx-4">
                <div class="h-6 bg-gray-300 rounded-full mb-2 shadow-inner"></div>
                <div class="h-4 bg-gray-500 rounded-full mb-2"></div>
                <div class="h-4 bg-gray-600 rounded-full mb-2"></div>
                <div class="h-6 bg-gray-400 rounded-full shadow-inner"></div>
            </div>
            
            <!-- Right Tassel -->
            <div class="relative">
                <div class="w-16 h-20 bg-gray-600 rounded-full transform rotate-12 opacity-80"></div>
                <div class="absolute top-2 right-2 w-12 h-16 bg-gray-700 rounded-full transform rotate-12"></div>
                <div class="absolute top-4 right-4 w-8 h-12 bg-gray-800 rounded-full transform rotate-12"></div>
            </div>
        </div>
    `;
}

async function handleAddToCart() {
    if (!currentProduct) return;

    // Ensure we know auth state
    if (!window.petHaven?.currentUser) {
        await window.petHaven.checkAuthStatus();
    }
    if (!window.petHaven.currentUser) {
        window.petHaven.showMessage('Please log in to add items to cart', 'error');
        setTimeout(() => { window.location.href = 'login.php'; }, 1500);
        return;
    }

    const quantityInput = document.getElementById('quantity-input');
    const quantity = Math.max(1, parseInt(quantityInput.value) || 1);

    try {
        const formData = new FormData();
        formData.append('productId', currentProduct.ProductID);
        formData.append('quantity', quantity);

        const response = await fetch('../process/cart.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            window.petHaven.showMessage(result.message, 'success');
            window.petHaven.loadCartCount();
            // Stay on the page; don't redirect unless you want to:
            // setTimeout(() => { window.location.href = 'cart.html'; }, 800);
        } else {
            window.petHaven.showMessage(result.message || 'Failed to add to cart', 'error');
        }
    } catch (err) {
        console.error(err);
        window.petHaven.showMessage('Failed to add to cart', 'error');
    }
}

function showProductNotFound() {
    document.getElementById('product-loading').classList.add('hidden');
    document.getElementById('product-not-found').classList.remove('hidden');
    document.getElementById('product-details').classList.add('hidden');
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