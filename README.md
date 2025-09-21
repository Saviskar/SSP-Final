# Pet Haven - Ecommerce Application

A comprehensive pet supplies ecommerce application built with PHP, MySQL, and JavaScript.

## Features

### User Features
- **User Registration & Authentication** - Secure registration and login system
- **Product Browsing & Search** - Browse products by category with advanced search
- **Shopping Cart Management** - Add, update, and remove items from cart
- **Order Placement** - Secure checkout with order confirmation
- **Promotions** - View discounted products and special offers

### Admin Features
- **Product Management** - Add, edit, and delete products
- **Inventory Management** - Real-time stock tracking and low stock alerts
- **Order Management** - View and manage customer orders
- **Promotion Management** - Create and manage product promotions
- **User Management** - View customer information and order history

## Project Structure

```
pet-haven/
├── admin/                  # Admin dashboard pages
│   ├── allproductsadmin.html
│   ├── addproduct.html
│   ├── promotions.html
│   └── orders.html
├── assets/                 # Static assets
│   ├── css/               # Stylesheets (Tailwind CSS)
│   ├── img/               # Images
│   ├── js/                # JavaScript files
│   └── uploads/           # Uploaded product images
├── process/               # Backend PHP handlers
│   ├── register.php       # User registration
│   ├── login.php          # User login
│   ├── logout.php         # User logout
│   ├── auth_check.php     # Authentication verification
│   ├── products.php       # Product management API
│   ├── cart.php           # Shopping cart API
│   ├── orders.php         # Order management API
│   ├── promotions.php     # Promotions API
│   └── inventory.php      # Inventory management API
├── public/                # Customer-facing pages
│   ├── landing.html       # Homepage
│   ├── allproducts.html   # Product catalog
│   ├── cart.html          # Shopping cart
│   ├── checkout.html      # Checkout page
│   ├── login.html         # Login page
│   └── createaccount.html # Registration page
├── src/                   # Source files
│   └── includes/          # PHP includes
│       ├── db.php         # Database connection
│       └── config.php     # Configuration settings
├── CreateTablesDDL.sql    # Database schema
├── InsertDataDDL.sql      # Sample data
└── README.md              # This file
```

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Database Setup

1. **Create Database:**
   ```sql
   CREATE DATABASE pethaven;
   ```

2. **Import Schema:**
   ```bash
   mysql -u root -p pethaven < CreateTablesDDL.sql
   ```

3. **Import Sample Data:**
   ```bash
   mysql -u root -p pethaven < InsertDataDDL.sql
   ```

### Application Setup

1. **Configure Database Connection:**
   Edit `src/includes/db.php` and update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'pethaven');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

2. **Set Permissions:**
   ```bash
   chmod 755 assets/uploads/
   chmod 644 assets/css/*
   chmod 644 assets/js/*
   ```

3. **Web Server Configuration:**
   
   **For Apache (.htaccess):**
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php [QSA,L]
   
   # Security headers
   Header always set X-Content-Type-Options nosniff
   Header always set X-Frame-Options DENY
   Header always set X-XSS-Protection "1; mode=block"
   ```

4. **CSS Framework:**
   The application uses Tailwind CSS. If you need to customize styles:
   ```bash
   npm install tailwindcss
   npx tailwindcss -i ./assets/css/input.css -o ./assets/css/output.css --watch
   ```

### Default Admin Credentials
- **Email:** admin@pethaven.test
- **Password:** 1234

*Note: Change these credentials immediately after setup.*

## API Endpoints

### Authentication
- `POST /process/register.php` - User registration
- `POST /process/login.php` - User login
- `GET /process/logout.php` - User logout
- `GET /process/auth_check.php` - Check authentication status

### Products
- `GET /process/products.php` - Get all products
- `POST /process/products.php` - Add new product (Admin only)
- `PUT /process/products.php` - Update product (Admin only)
- `DELETE /process/products.php` - Delete product (Admin only)

### Shopping Cart
- `GET /process/cart.php` - Get cart items
- `POST /process/cart.php` - Add item to cart
- `PUT /process/cart.php` - Update cart item quantity
- `DELETE /process/cart.php` - Remove item from cart

### Orders
- `GET /process/orders.php` - Get user orders
- `POST /process/orders.php` - Place new order

### Promotions (Admin only)
- `GET /process/promotions.php` - Get all promotions
- `POST /process/promotions.php` - Create promotion
- `PUT /process/promotions.php` - Update promotion
- `DELETE /process/promotions.php` - Delete promotion

### Inventory (Admin only)
- `GET /process/inventory.php` - Get inventory status
- `PUT /process/inventory.php` - Update stock levels
- `DELETE /process/inventory.php` - Delete product

## Database Schema

### Core Tables
- **User** - Customer and admin accounts
- **Product** - Product catalog
- **Category** - Product categories
- **Cart** - Shopping carts
- **CartItem** - Items in shopping carts
- **Order** - Customer orders
- **OrderItem** - Items in orders
- **Promotion** - Discount promotions
- **ProductPromotion** - Product-promotion relationships

### Address Management
- **Province** - Geographic provinces
- **City** - Cities within provinces
- **UserAddress** - Customer delivery addresses

## Security Features

- **Password Hashing** - Secure password storage using PHP's password_hash()
- **SQL Injection Prevention** - Prepared statements throughout
- **XSS Protection** - Input sanitization and output encoding
- **Session Management** - Secure session handling
- **CSRF Protection** - Token-based CSRF prevention
- **Admin Authentication** - Role-based access control

## Frontend Features

- **Responsive Design** - Mobile-first responsive layout
- **Real-time Updates** - Dynamic cart and inventory updates
- **Search & Filtering** - Product search and category filtering
- **Interactive UI** - Modern JavaScript interactions
- **Error Handling** - User-friendly error messages
- **Loading States** - Visual feedback for async operations

## Testing

### Test User Accounts
- **Customer:** alice@example.com / 1234
- **Customer:** bob@example.com / 1234
- **Admin:** admin@pethaven.test / 1234

### Sample Data Included
- 6 products across 4 categories
- 2 promotions with discounts
- Sample orders and cart items
- Address data for testing

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `src/includes/db.php`
   - Ensure MySQL server is running
   - Verify database exists and user has permissions

2. **Session Issues**
   - Check PHP session configuration
   - Ensure proper file permissions
   - Clear browser cookies

3. **CSS Not Loading**
   - Verify file paths in HTML files
   - Check web server configuration
   - Ensure CSS files have proper permissions

4. **JavaScript Errors**
   - Check browser console for errors
   - Verify API endpoints are accessible
   - Ensure proper CORS configuration

### Performance Optimization

1. **Database Indexes** - Add indexes for frequently queried columns
2. **Image Optimization** - Compress and resize product images
3. **Caching** - Implement PHP OPcache for better performance
4. **CDN** - Use CDN for static assets in production

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is for educational purposes. Please check licensing requirements for production use.

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review the code documentation
3. Test with sample data provided
4. Ensure all setup steps were completed correctly