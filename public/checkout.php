<?php
// === Bootstrap & auth (must run before any output) ===
require_once __DIR__ . '/../src/includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); // or your login route
  exit;
}

$userId = (int)$_SESSION['user_id'];
$db = getDB();

// Fetch profile + (optional) address
$st = $db->prepare("
  SELECT 
    u.FullName, u.Email, u.Mobile,
    a.AddressLine, a.CityID
  FROM User u
  LEFT JOIN UserAddress a ON a.UserID = u.UserID
  WHERE u.UserID = ?
  LIMIT 1
");
$st->execute([$userId]);
$profile = $st->fetch() ?: [];

// Helper to keep old POST on validation error, else DB value
function old($key, $fallback = '') {
  return htmlspecialchars($_POST[$key] ?? ($fallback ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<?php require __DIR__ . '/../includes/header.php'; ?>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
  <nav class="flex text-sm">
    <a href="cart.html" class="text-red-500 hover:text-red-600">Cart</a>
    <span class="mx-2 text-gray-500">/</span>
    <span class="text-gray-900">Checkout</span>
  </nav>
</div>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <!-- Form and Order Summary -->
    <!-- If your checkout.js handles submission via JS, you can keep action empty -->
    <form id="checkout-form" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Customer Information Form -->
      <div class="space-y-6">
        <h2 class="text-xl font-semibold text-gray-900">Delivery Information</h2>

        <!-- Full Name and Email Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input
              type="text"
              id="fullName"
              name="fullName"
              placeholder="Enter your full name"
              required
              value="<?= old('fullName', $profile['FullName'] ?? '') ?>"
              autocomplete="name"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"
            >
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Enter your email"
              required
              value="<?= old('email', $profile['Email'] ?? '') ?>"
              autocomplete="email"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"
            >
          </div>
        </div>

        <!-- Mobile Number and Delivery Address Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
            <input
              type="tel"
              id="mobile"
              name="mobile"
              placeholder="Enter your mobile number"
              required
              value="<?= old('mobile', $profile['Mobile'] ?? '') ?>"
              autocomplete="tel"
              inputmode="numeric"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"
            >
          </div>
          <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Delivery Address</label>
            <input
              type="text"
              id="address"
              name="address"
              placeholder="Enter your delivery address"
              required
              value="<?= old('address', $profile['AddressLine'] ?? '') ?>"
              autocomplete="street-address"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"
            >
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>

        <!-- Loading State -->
        <div id="order-loading" class="flex items-center justify-center py-12">
          <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500 mx-auto mb-2"></div>
            <p class="text-gray-500 text-sm">Loading order details...</p>
          </div>
        </div>

        <!-- Order Items -->
        <div id="order-items" class="space-y-4 mb-6 hidden">
          <!-- Items will be loaded dynamically -->
        </div>

        <!-- Order Totals -->
        <div id="order-totals" class="border-t border-gray-200 pt-4 space-y-3 hidden">
          <!-- Totals will be loaded dynamically -->
        </div>

        <!-- Payment Notice -->
        <div class="mt-6 p-3 bg-gray-50 rounded-lg">
          <p class="text-sm text-gray-600">Payment will be collected upon delivery (Cash on Delivery).</p>
        </div>

        <!-- Place Order Button -->
        <button
          type="submit"
          class="w-full mt-6 bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
          disabled
          id="place-order-btn"
        >
          <span id="place-order-text">Loading...</span>
        </button>
      </div>
    </form>
  </div>
</main>

<script src="../assets/js/main.js"></script>
<script src="../assets/js/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var btn = document.getElementById('mobile-menu-button');
  if (btn && btn.type !== 'button') btn.type = 'button';
});
</script>


</body>
</html>
