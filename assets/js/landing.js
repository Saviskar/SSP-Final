/* ------------------------------
   Image maps (edit paths as you like)
------------------------------ */
const categoryImages = {
  'Dog Food':        '/assets/img/categories/dog-food.png',
  'Cat Food':        '/assets/img/categories/cat-food.jpg',
  'Cat Litter':      '/assets/img/categories/cat-litter.jpg',
  'Dog Treats':      '/assets/img/categories/dog-treats.jpg',
  'Accessories':     '/assets/img/categories/accessories.jpg',
  'Grooming':        '/assets/img/categories/grooming.jpg',
  'Shampoos':        '/assets/img/categories/shampoos.jpg'
};

// Optional product-level images for promos (used if backend doesn’t provide ImageUrl)
const productImages = {
  // 'Pedigree 5.44kg': '../assets/img/products/dogs/dry/pedigree.webp',
  // 'VitaPet 3kg':     '../assets/img/products/dogs/dry/vitapet-3kg.jpg',
};

const FALLBACK_IMG = '../assets/img/categories/fallback.jpg';

/* ------------------------------
   Boot
------------------------------ */
document.addEventListener('DOMContentLoaded', function() {
  loadCategories();
  loadPromotionalProducts();
  setupEventListeners();
});

/* ------------------------------
   Events
------------------------------ */
function setupEventListeners() {
  // Mobile menu toggle
  document.getElementById('mobile-menu-button').addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
  });

  // Category clicks (delegated)
  document.addEventListener('click', function(e) {
    const catEl = e.target.closest('.category-item');
    if (catEl) {
      const categoryName = catEl.dataset.category;
      if (categoryName) {
        window.location.href = `allproducts.html?category=${encodeURIComponent(categoryName)}`;
      }
    }
  });
}

/* ------------------------------
   API: Categories
------------------------------ */
async function loadCategories() {
  try {
    const response = await fetch('../process/products.php?limit=5');
    const result = await response.json();
    if (result.success && result.data?.categories) {
      displayCategories(result.data.categories.slice(0, 5));
    }
  } catch (error) {
    console.error('Error loading categories:', error);
  }
}

function displayCategories(categories) {
  const container = document.getElementById('categories-section');
  if (!container) return;

  container.innerHTML = categories.map(category => {
    const name = category.CategoryName || category.name || 'Category';

    // Accept ImageURL, ImageUrl, image_url
    const apiImg = category.ImageURL ?? category.ImageUrl ?? category.image_url ?? null;
    const imgSrc = apiImg || categoryImages[name] || FALLBACK_IMG;

    return `
      <div class="category-item bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow p-4 text-center group cursor-pointer"
           data-category="${name}" title="${name}">
        <div class="w-full h-40 md:h-44 rounded-xl overflow-hidden mb-3">
          <img
            src="${imgSrc}"
            alt="${name}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
            decoding="async"
            onerror="this.onerror=null;this.src='${FALLBACK_IMG}'"
          />
        </div>
        <h3 class="font-semibold text-gray-800 group-hover:text-red-500 transition-colors">${name}</h3>
      </div>
    `;
  }).join('');
}


/* ------------------------------
   API: Promotions
------------------------------ */
async function loadPromotionalProducts() {
  try {
    const response = await fetch('../process/promotions.php');
    const result = await response.json();
    if (result.success && result.data?.promotions) {
      displayPromotionalProducts(result.data.promotions.slice(0, 5));
    } else {
      displayStaticPromotions();
    }
  } catch (error) {
    console.error('Error loading promotions:', error);
    displayStaticPromotions();
  }
}

function displayPromotionalProducts(promotions) {
  const container = document.getElementById('promotions-section');
  if (!Array.isArray(promotions) || promotions.length === 0) {
    displayStaticPromotions();
    return;
  }

  container.innerHTML = promotions.map(promo => {
    const name = promo.ProductName || promo.name || 'Promotion';
    // Use backend image if provided; else product map; else category map; else fallback
    const imgSrc =
      promo.ImageUrl || promo.image_url ||
      productImages[name] ||
      categoryImages[name] ||
      FALLBACK_IMG;

    const discount = promo.PromotionPercentage ?? promo.discount ?? 0;

    return `
      <div
        class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow p-4 text-center group cursor-pointer relative"
        onclick="window.location.href='allproducts.html'"
        title="${name}"
      >
        <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
          ${discount}% OFF
        </div>
        <div class="w-full h-40 md:h-44 rounded-xl overflow-hidden mb-3">
          <img
            src="${imgSrc}"
            alt="${name}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
            decoding="async"
          />
        </div>
        <h3 class="font-semibold text-gray-800 group-hover:text-red-500 transition-colors">${name}</h3>
      </div>
    `;
  }).join('');
}


  //  Static fallback promotions
function displayStaticPromotions() {
  const container = document.getElementById('promotions-section');
  const staticPromotions = [
    { name: 'Dog Food',   discount: 20 },
    { name: 'Cat Food',   discount: 15 },
    { name: 'Cat Litter', discount: 10 },
    { name: 'Dog Treats', discount: 25 },
    { name: 'Shampoos',   discount: 30 }
  ];

  container.innerHTML = staticPromotions.map(promo => {
    const imgSrc = categoryImages[promo.name] || FALLBACK_IMG;
    return `
      <div
        class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow p-4 text-center group cursor-pointer relative"
        onclick="window.location.href='allproducts.html'"
        title="${promo.name}"
      >
        <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
          ${promo.discount}% OFF
        </div>
        <div class="w-full h-40 md:h-44 rounded-xl overflow-hidden mb-3">
          <img
            src="${imgSrc}"
            alt="${promo.name}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
            decoding="async"
          />
        </div>
        <h3 class="font-semibold text-gray-800 group-hover:text-red-500 transition-colors">${promo.name}</h3>
      </div>
    `;
  }).join('');
}

//  Logout (unchanged except a tiny optional chaining to avoid errors)
async function logout() {
  try {
    const response = await fetch('../process/logout.php');
    const result = await response.json();
    if (result.success) {
      window.petHaven?.showMessage?.('Logged out successfully', 'success');
      setTimeout(() => {
        window.location.reload();
      }, 800);
    }
  } catch (error) {
    console.error('Logout error:', error);
  }
}