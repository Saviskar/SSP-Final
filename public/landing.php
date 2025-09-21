<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="relative bg-gray-600 rounded-3xl overflow-hidden min-h-96" style="background-image: url('https://images.unsplash.com/photo-1601758228041-f3b2795255f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center;">
        <!-- Background Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        
        <!-- Hero Content -->
        <div class="relative z-10 flex flex-col items-center justify-center text-center px-4 py-16 min-h-96">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                Welcome to Pet Haven
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-2xl leading-relaxed">
                Your one-stop shop for all your pet's needs. Explore our wide range of products and services.
            </p>
            <a href="allproducts.php" class="bg-red-500 hover:bg-red-600 text-white font-bold px-8 py-3 rounded-lg transition-colors text-lg">
                Shop Now
            </a>
        </div>
    </div>
</section>

<!-- Our Categories Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8">Our Categories</h2>
    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6" id="categories-section">
        <!-- Populated by JS -->
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center col-span-2 md:col-span-1">
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
    </div>
</section>

<!-- Our Promotions Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8">Our Promotions</h2>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6" id="promotions-section">
        <!-- Populated by JS -->
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center relative">
            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">SALE</div>
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center relative">
            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">SALE</div>
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center relative">
            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">SALE</div>
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center relative">
            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">SALE</div>
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center col-span-2 md:col-span-1 relative">
            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">SALE</div>
            <div class="w-full h-32 bg-gray-100 rounded-xl mb-4 animate-pulse"></div>
            <div class="animate-pulse bg-gray-300 h-4 rounded w-20 mx-auto"></div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>