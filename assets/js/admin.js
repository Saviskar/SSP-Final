class AdminPanel {
    constructor() {
        this.currentView = 'products';
        this.products = [];
        this.promotions = [];
        this.orders = [];
        this.categories = [];
        this.init();
    }

    async init() {
        this.setupNavigation();
        this.setupFileUpload();
        await this.loadCategories();
        await this.loadProducts();
        this.showView('products');
    }

    setupNavigation() {
        document.querySelectorAll('[data-nav]').forEach(nav => {
            nav.addEventListener('click', (e) => {
                e.preventDefault();
                const view = nav.getAttribute('data-nav');
                this.showView(view);
            });
        });

        // Setup modal close
        document.getElementById('closeModal')?.addEventListener('click', () => {
            this.closeModal();
        });

        // Close modal when clicking outside
        document.getElementById('productModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'productModal') {
                this.closeModal();
            }
        });

        // Setup form submissions
        document.getElementById('productForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleProductSubmit();
        });

        // [ADDED] Wire “New promotion” inside the class so `this` stays correct
        const btnNewPromo = document.getElementById('btnNewPromotion');
        if (btnNewPromo) {
            btnNewPromo.addEventListener('click', () => this.createPromotion());
        }
    }

    async showView(view) {
        this.currentView = view;
        
        // Update desktop navigation
        document.querySelectorAll('aside [data-nav]').forEach(nav => {
            nav.parentElement.classList.remove('bg-gray-300');
            nav.parentElement.classList.add('hover:bg-gray-200');
        });
        
        // Update mobile navigation  
        document.querySelectorAll('.lg\\:hidden [data-nav]').forEach(nav => {
            nav.classList.remove('bg-gray-300');
        });

        // Set active states
        const desktopNav = document.querySelector(`aside [data-nav="${view}"]`);
        if (desktopNav) {
            desktopNav.parentElement.classList.add('bg-gray-300');
            desktopNav.parentElement.classList.remove('hover:bg-gray-200');
        }

        const mobileNav = document.querySelector(`.lg\\:hidden [data-nav="${view}"]`);
        if (mobileNav) {
            mobileNav.classList.add('bg-gray-300');
        }

        // Show content
        document.querySelectorAll('.content-view').forEach(content => {
            content.classList.add('hidden');
        });
        
        const contentDiv = document.getElementById(`${view}Content`);
        if (contentDiv) {
            contentDiv.classList.remove('hidden');
        }

        // Load data for the view
        switch(view) {
            case 'products':
                await this.loadProducts();
                this.renderProducts();
                break;
            case 'promotions':
                await this.loadPromotions();
                this.renderPromotions();
                break;
            case 'orders':
                await this.loadOrders();
                this.renderOrders();
                break;
        }
    }

    async loadProducts() {
        try {
            const response = await fetch('../process/admin_products.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.products = await response.json();
            console.log('Products loaded:', this.products);
        } catch (error) {
            console.error('Error loading products:', error);
            this.products = [];
        }
    }

    async loadPromotions() {
        try {
            const response = await fetch('../process/admin_promotions.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.promotions = await response.json();
            console.log('Promotions loaded:', this.promotions);
        } catch (error) {
            console.error('Error loading promotions:', error);
            this.promotions = [];
        }
    }

    async loadOrders() {
        try {
            const response = await fetch('../process/admin_orders.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.orders = await response.json();
            console.log('Orders loaded:', this.orders);
        } catch (error) {
            console.error('Error loading orders:', error);
            this.orders = [];
        }
    }

    async loadCategories() {
        try {
            const response = await fetch('../process/admin_categories.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.categories = await response.json();
            console.log('Categories loaded:', this.categories);
        } catch (error) {
            console.error('Error loading categories:', error);
            this.categories = [];
        }
    }

    renderProducts() {
        const tbody = document.getElementById('productsTableBody');
        if (!tbody) {
            console.error('Products table body not found');
            return;
        }

        if (!Array.isArray(this.products) || this.products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">No products found</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = this.products.map(product => `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4">${product.ProductID}</td>
                <td class="p-4 text-red-400">${product.ProductName || 'N/A'}</td>
                <td class="p-4 text-gray-600">${product.Stock || 0}</td>
                <td class="p-4 text-gray-600">$${parseFloat(product.Price || 0).toFixed(2)}</td>
                <td class="p-4">
                    <button onclick="adminPanel.editProduct(${product.ProductID})" class="text-red-500 mr-4 hover:text-red-700">Edit</button>
                    <button onclick="adminPanel.deleteProduct(${product.ProductID})" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 8a1 1 0 012 0v3a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v3a1 1 0 11-2 0V8z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    renderPromotions() {
        const tbody = document.getElementById('promotionsTableBody');
        if (!tbody) {
            console.error('Promotions table body not found');
            return;
        }

        if (!Array.isArray(this.promotions) || this.promotions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="p-4 text-center text-gray-500">No promotions found</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = this.promotions.map(promotion => `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4">${promotion.ProductID}</td>
                <td class="p-4 text-gray-600">${promotion.Discount}%</td>
                <td class="p-4">
                    <button onclick="adminPanel.editPromotion(${promotion.PromotionID})" class="text-red-500 mr-4 hover:text-red-700">Edit</button>
                    <button onclick="adminPanel.deletePromotion(${promotion.PromotionID})" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 8a1 1 0 012 0v3a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v3a1 1 0 11-2 0V8z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    renderOrders() {
    const tbody = document.getElementById('ordersTableBody');
    if (!tbody) {
        console.error('Orders table body not found');
        return;
    }

    if (!Array.isArray(this.orders) || this.orders.length === 0) {
        // [CHANGED] colspan 7 -> 6 (we removed one column)
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="p-4 text-center text-gray-500">No orders found</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = this.orders.map(order => {
        let statusClass = 'bg-orange-500';
        if (order.Status === 'shipped') statusClass = 'bg-blue-500';
        if (order.Status === 'delivered') statusClass = 'bg-green-500';
        if (order.Status === 'cancelled') statusClass = 'bg-red-500';

        return `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4">${order.OrderID}</td>
                <td class="p-4 text-red-400">${order.Customer || 'N/A'}</td>
                <td class="p-4 text-gray-600">${order.OrderPlacedDate || 'N/A'}</td>
                <!-- [REMOVED] ProductsIDxQuantity column -->
                <td class="p-4 text-gray-600">${order.Total || '$0'}</td>
                <td class="p-4">
                    <span class="${statusClass} text-white px-3 py-1 rounded-full text-sm capitalize">${order.Status || 'unknown'}</span>
                </td>
                <td class="p-4 text-gray-600">${order.DeliveryAddress || 'N/A'}</td>
            </tr>
        `;
    }).join('');
    }


    showAddProductModal() {
        document.getElementById('productModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Add New Product';
        document.getElementById('productForm').reset();
        delete document.getElementById('productForm').dataset.imageUrl;
        this.renderCategoryOptions();
        this.resetFileUpload();
    }

    resetFileUpload() {
        const dropZone = document.getElementById('dropZone');
        if (dropZone) {
            dropZone.innerHTML = `
                <div class="space-y-2">
                    <p class="text-gray-600">Drag and drop or click to upload</p>
                    <button type="button" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Upload
                    </button>
                </div>
            `;
        }
    }

    renderCategoryOptions() {
        const select = document.getElementById('categorySelect');
        if (!select) return;

        select.innerHTML = '<option value="">Select Category</option>' + 
            this.categories.map(cat => 
                `<option value="${cat.CategoryID}">${cat.CategoryName}</option>`
            ).join('');
    }

    closeModal() {
        document.getElementById('productModal').classList.add('hidden');
    }

    async handleProductSubmit() {
        const formData = new FormData(document.getElementById('productForm'));
        const form = document.getElementById('productForm');
        const data = {
            productName: formData.get('productName'),
            categoryId: formData.get('categoryId'),
            price: formData.get('price'),
            stock: formData.get('stock'),
            description: formData.get('description'),
            imageUrl: form.dataset.imageUrl || null
        };

        console.log('Submitting product data:', data);

        try {
            const response = await fetch('../process/admin_products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Product submission result:', result);

            if (response.ok && result.success) {
                this.closeModal();
                await this.loadProducts();
                this.renderProducts();
                alert('Product added successfully!');
            } else {
                alert('Error adding product: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error adding product:', error);
            alert('Error adding product: ' + error.message);
        }
    }

    // [CHANGED] Implemented the class method so your existing onclick="adminPanel.editProduct(id)" works
    async editProduct(productId) {
        try {
            // load current product
            const res = await fetch(`../process/admin_products.php?productId=${productId}`);
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.message || 'Failed to load product');
            const p = json.data;

            // prompt-based quick edit
            const name = prompt('Name:', p.Name ?? p.ProductName ?? p.name ?? '');
            if (name === null) return;

            const priceStr = prompt('Price:', String(p.Price ?? p.price ?? '0'));
            if (priceStr === null) return;
            const price = Number(priceStr);
            if (Number.isNaN(price) || price <= 0) { alert('Invalid price'); return; }

            const categoryStr = prompt('Category ID:', String(p.CategoryID ?? p.categoryId ?? ''));
            if (categoryStr === null) return;
            const categoryId = Number(categoryStr);
            if (!categoryId) { alert('Invalid category'); return; }

            const description = prompt('Description:', p.Description ?? p.description ?? '');
            if (description === null) return;

            const isActiveStr = prompt('Active? (1=yes, 0=no):', String((p.IsActive ?? p.isActive ?? 1) ? 1 : 0));
            if (isActiveStr === null) return;
            const isActive = Number(isActiveStr) ? 1 : 0;

            const payload = { productId, name: name.trim(), price, categoryId, description: description.trim(), isActive };

            // save (PUT JSON)
            let save = await fetch('../process/admin_products.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            // fallback if server blocks PUT
            if (!save.ok) {
                save = await fetch('../process/admin_products.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ _method: 'PUT', ...payload })
                });
            }

            const out = await save.json();
            if (!out.success) throw new Error(out.message || 'Update failed');

            alert('Product updated ✅');
            await this.loadProducts();
            this.renderProducts();
        } catch (e) {
            console.error(e);
            alert('Error editing product: ' + e.message);
        }
    }

    async deleteProduct(productId) {
        if (!confirm('Are you sure you want to delete this product?')) return;

        try {
            const response = await fetch('../process/admin_products.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ productId })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                await this.loadProducts();
                this.renderProducts();
                alert('Product deleted successfully!');
            } else {
                alert('Error deleting product: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error deleting product:', error);
            alert('Error deleting product: ' + error.message);
        }
    }

    async editPromotion(promotionId) {
        // For now, just show an alert - you can implement edit functionality later
        alert(`Edit promotion functionality for ID ${promotionId} would be implemented here`);
    }

    async deletePromotion(promotionId) {
        if (!confirm('Are you sure you want to delete this promotion?')) return;

        try {
            const response = await fetch('../process/admin_promotions.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ promotionId })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                await this.loadPromotions();
                this.renderPromotions();
                alert('Promotion deleted successfully!');
            } else {
                alert('Error deleting promotion: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error deleting promotion:', error);
            alert('Error deleting promotion: ' + error.message);
        }
    }

    // File upload handling
    setupFileUpload() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('imageUpload');

        if (!dropZone || !fileInput) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            this.handleFiles(files);
        }, false);

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            this.handleFiles(e.target.files);
        });
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            const formData = new FormData();
            formData.append('image', file);
            
            // Show upload progress
            const dropZone = document.getElementById('dropZone');
            dropZone.innerHTML = '<p class="text-gray-600">Uploading...</p>';
            
            fetch('../process/admin_upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    dropZone.innerHTML = `
                        <p class="text-green-600">✓ Upload successful</p>
                        <p class="text-sm text-gray-500">${file.name}</p>
                    `;
                    // Store the uploaded file URL for form submission
                    document.getElementById('productForm').dataset.imageUrl = data.fileUrl;
                } else {
                    dropZone.innerHTML = `
                        <p class="text-red-600">Upload failed: ${data.error}</p>
                        <button type="button" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors mt-2" onclick="adminPanel.resetFileUpload()">
                            Try Again
                        </button>
                    `;
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                dropZone.innerHTML = `
                    <p class="text-red-600">Upload failed</p>
                    <button type="button" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors mt-2" onclick="adminPanel.resetFileUpload()">
                        Try Again
                    </button>
                `;
            });
        }
    }

    // [ADDED] Class method to create a promotion; replaces the old global listener
    async createPromotion() {
        const productId = Number(prompt('Enter Product ID to discount:'));
        const discount  = Number(prompt('Enter discount % (0–90):'));
        if (!productId || Number.isNaN(productId) || Number.isNaN(discount) || discount < 0 || discount > 90) return;

        try {
            const res = await fetch('../process/admin_promotions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ productId, discount })
            });

            let json = null;
            try { json = await res.json(); } catch {}
            const ok = res.ok && (json?.success === undefined || json.success === true);
            if (!ok) {
                const msg = (json && (json.error || json.message)) || `HTTP ${res.status}`;
                throw new Error(msg);
            }

            await this.loadPromotions();
            this.renderPromotions();
            alert('Promotion created successfully!');
        } catch (e) {
            console.error(e);
            alert('Error creating promotion: ' + e.message);
        }
    }
}

// [REMOVED] Old global click handler for #btnNewPromotion that used wrong `this`
// (It caused a false “error” message even when the API succeeded.)

// (Optional helpers from earlier messages can remain global if you still use them elsewhere)

// Initialize admin panel when DOM is loaded
let adminPanel;
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing admin panel...');
    adminPanel = new AdminPanel();
});
