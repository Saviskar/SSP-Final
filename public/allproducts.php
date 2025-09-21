<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <nav class="flex text-sm overflow-x-auto whitespace-nowrap scrollbar-hide">
        <a href="landing.php" class="text-red-500 hover:text-red-600 flex-shrink-0">Home</a>
        <span class="mx-2 text-gray-500 flex-shrink-0">/</span>
        <a href="allproducts.php" class="text-gray-900 flex-shrink-0">All Products</a>
    </nav>
</div>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">All Products</h1>
    
    <!-- Category Filter Tabs -->
    <div class="flex gap-2 mb-8 overflow-x-auto whitespace-nowrap scrollbar-hide pb-2" id="categories-container">
        <button class="category-filter px-4 py-2 bg-red-100 text-red-600 rounded-full text-sm font-medium flex-shrink-0" data-category="">All Products</button>
        <!-- Categories will be loaded dynamically -->
    </div>
    
    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6" id="products-container">
        <!-- Products will be loaded dynamically -->
        <div class="flex items-center justify-center col-span-full py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
                <p class="text-gray-500">Loading products...</p>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
