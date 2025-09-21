<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-2xl mx-auto">
        <!-- Page Title -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900">Create your account</h1>
        </div>
        
        <!-- Registration Form -->
        <form id="register-form" class="space-y-6">
            <!-- Full Name and Email Address Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors placeholder-gray-400">
                </div>
                <div>
                    <label for="emailAddress" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="emailAddress" name="emailAddress" placeholder="Enter your email address" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors placeholder-gray-400">
                </div>
            </div>
            
            <!-- Mobile Number and Delivery Address Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="mobileNumber" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input type="tel" id="mobileNumber" name="mobileNumber" placeholder="Enter your mobile number" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors placeholder-gray-400">
                </div>
                <div>
                    <label for="deliveryAddress" class="block text-sm font-medium text-gray-700 mb-2">Delivery Address</label>
                    <input type="text" id="deliveryAddress" name="deliveryAddress" placeholder="Enter your delivery address" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors placeholder-gray-400">
                </div>
            </div>
            
            <!-- Password Field -->
            <div class="max-w-md">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors placeholder-gray-400">
                <p class="text-sm text-gray-500 mt-1">Password must be at least 6 characters long</p>
            </div>
            
            <!-- Create Account Button -->
            <div class="pt-4">
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-8 rounded-lg transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Create Account
                </button>
            </div>
            
            <!-- Sign In Link -->
            <div class="pt-2">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-red-500 hover:text-red-600 font-medium">Sign in</a>
                </p>
            </div>
        </form>
    </div>
</main>

<script src="../assets/js/main.js"></script>
<script>
// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});
</script>

</body>
</html>