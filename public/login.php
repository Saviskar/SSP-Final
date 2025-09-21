<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-md mx-auto">
        <!-- Page Title -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900">Welcome back</h1>
        </div>
        
        <!-- Login Form -->
        <form id="login-form" method="post" class="space-y-6">
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors placeholder-gray-400">
            </div>
            
            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors placeholder-gray-400">
            </div>
            
            <!-- Forgot Password Link -->
            <div class="text-left">
                <a href="#" class="text-gray-600 hover:text-gray-800 text-sm">Forgot password?</a>
            </div>
            
            <!-- Log In Button -->
            <div class="pt-4">
                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Log in
                </button>
            </div>
            
            <!-- Create Account Link -->
            <div class="text-center pt-2">
                <p class="text-gray-600 text-sm">
                    New to Pet Haven? 
                    <a href="createaccount.html" class="text-red-500 hover:text-red-600 font-medium">Create an account</a>
                </p>
            </div>
        </form>
    </div>
</main>


<script>
// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});
</script> 

</body>
</html>