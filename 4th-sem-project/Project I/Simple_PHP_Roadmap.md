# Farm2Door - Simple PHP Implementation Guide

## 🎯 **No OOP Required!** 
This guide uses **procedural PHP** - just functions and variables, no classes or objects.

## 📁 **Simple File Structure**

```
Project I/
├── Frontend/ (Already completed)
├── Backend/
│   ├── config/
│   │   └── database_simple.php          # Database connection functions
│   ├── includes/
│   │   ├── auth_simple.php              # Login/register functions
│   │   └── functions_simple.php         # Helper functions
│   ├── api/
│   │   ├── cart/
│   │   │   └── add.php                  # Add to cart
│   │   └── products/
│   │       └── list.php                 # Get products
│   ├── dashboard/
│   │   ├── farmer/
│   │   │   └── index.php                # Farmer dashboard
│   │   └── consumer/
│   │       └── index.php                # Consumer dashboard
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   ├── uploads/
│   │   └── products/
│   ├── login.php                        # Login page
│   ├── register.php                     # Registration page
│   ├── products.php                     # Products listing
│   ├── product.php                      # Single product view
│   ├── cart.php                         # Shopping cart
│   ├── checkout.php                     # Checkout process
│   └── logout.php                       # Logout
└── Database/
    ├── schema.sql                       # Database structure
    └── sample_data.sql                  # Test data
```

## 🗄️ **Database Setup (Same as Before)**

1. **Create MySQL Database:**
   ```sql
   CREATE DATABASE farm2door;
   USE farm2door;
   ```

2. **Import Schema:**
   ```bash
   mysql -u root -p farm2door < Database/schema.sql
   ```

3. **Import Sample Data:**
   ```bash
   mysql -u root -p farm2door < Database/sample_data.sql
   ```

## 🚀 **Implementation Steps (No OOP!)**

### **Step 1: Basic Setup (Week 1)**

#### **1.1 Database Connection**
```php
// config/database_simple.php
function connectDatabase() {
    global $db_connection;
    $db_connection = mysqli_connect('localhost', 'root', '', 'farm2door');
    return $db_connection;
}
```

#### **1.2 Simple Functions**
```php
// includes/functions_simple.php
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}
```

#### **1.3 Login System**
```php
// includes/auth_simple.php
function loginUser($email, $password) {
    // Check credentials
    // Set session variables
    // Return success/error
}

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
```

### **Step 2: User Management (Week 2)**

#### **2.1 Registration Page**
- Create `register.php`
- Use `registerUser()` function
- Form validation
- Success/error messages

#### **2.2 Login Page**
- Create `login.php` (already provided)
- Use `loginUser()` function
- Redirect based on user type

#### **2.3 User Dashboard**
- Create `dashboard/farmer/index.php`
- Create `dashboard/consumer/index.php`
- Display user-specific content

### **Step 3: Product Management (Week 3)**

#### **3.1 Products Listing**
- Create `products.php` (already provided)
- Search and filter functionality
- Pagination
- Product cards

#### **3.2 Single Product View**
- Create `product.php`
- Product details
- Add to cart functionality
- Reviews display

#### **3.3 Product CRUD (for farmers)**
- Add product form
- Edit product form
- Delete product
- Image upload

### **Step 4: Shopping Cart (Week 4)**

#### **4.1 Cart Functions**
```php
// includes/cart_functions.php
function addToCart($user_id, $product_id, $quantity) {
    // Add item to cart
}

function getCartItems($user_id) {
    // Get cart items
}

function updateCartItem($user_id, $product_id, $quantity) {
    // Update cart item
}
```

#### **4.2 Cart Page**
- Create `cart.php`
- Display cart items
- Update quantities
- Remove items
- Calculate total

### **Step 5: Order System (Week 5)**

#### **5.1 Order Functions**
```php
// includes/order_functions.php
function createOrder($user_id, $cart_items) {
    // Create order from cart
}

function getOrders($user_id) {
    // Get user orders
}

function updateOrderStatus($order_id, $status) {
    // Update order status
}
```

#### **5.2 Checkout Process**
- Create `checkout.php`
- Order summary
- Delivery information
- Payment processing

### **Step 6: Dashboard Features (Week 6)**

#### **6.1 Farmer Dashboard**
- Product management
- Order tracking
- Sales statistics
- Profile management

#### **6.2 Consumer Dashboard**
- Order history
- Profile management
- Favorite products
- Address book

## 💻 **Simple PHP Examples**

### **Database Query (No OOP)**
```php
// Get all products
function getAllProducts() {
    $conn = connectDatabase();
    $query = "SELECT * FROM products WHERE is_available = 1";
    $result = mysqli_query($conn, $query);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    return $products;
}
```

### **User Authentication (No OOP)**
```php
// Check if user is logged in
if (isUserLoggedIn()) {
    $user_type = getCurrentUserType();
    if ($user_type === 'farmer') {
        redirect('/dashboard/farmer/');
    } else {
        redirect('/dashboard/consumer/');
    }
}
```

### **Form Processing (No OOP)**
```php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    $result = loginUser($email, $password);
    
    if ($result['success']) {
        redirect('/dashboard/');
    } else {
        $error_message = $result['message'];
    }
}
```

## 🛠️ **Required PHP Knowledge**

### **What You Need to Know:**
1. **Variables and Arrays** - `$variable`, `$_POST`, `$_GET`
2. **Functions** - `function myFunction() { }`
3. **Conditionals** - `if`, `else`, `switch`
4. **Loops** - `for`, `while`, `foreach`
5. **MySQL** - `mysqli_connect()`, `mysqli_query()`
6. **Sessions** - `session_start()`, `$_SESSION`
7. **Forms** - `$_POST`, `$_GET`, form validation

### **What You DON'T Need:**
- ❌ Classes and Objects
- ❌ Inheritance
- ❌ Polymorphism
- ❌ Encapsulation
- ❌ Abstract classes
- ❌ Interfaces

## 📋 **Implementation Checklist**

### **Week 1: Foundation**
- [ ] Set up database connection
- [ ] Create basic functions
- [ ] Implement login system
- [ ] Create user registration

### **Week 2: User Management**
- [ ] User dashboard
- [ ] Profile management
- [ ] Password change
- [ ] Logout functionality

### **Week 3: Products**
- [ ] Product listing
- [ ] Search and filter
- [ ] Product details
- [ ] Image upload

### **Week 4: Shopping Cart**
- [ ] Add to cart
- [ ] Cart management
- [ ] Quantity updates
- [ ] Cart persistence

### **Week 5: Orders**
- [ ] Checkout process
- [ ] Order creation
- [ ] Order tracking
- [ ] Order history

### **Week 6: Dashboard**
- [ ] Farmer dashboard
- [ ] Consumer dashboard
- [ ] Statistics
- [ ] Advanced features

## 🎯 **Benefits of Procedural PHP**

### **Advantages:**
- ✅ **Easier to Learn** - No complex concepts
- ✅ **Faster Development** - Direct approach
- ✅ **Better for Beginners** - Straightforward logic
- ✅ **Less Code** - No boilerplate
- ✅ **Easier Debugging** - Linear flow

### **Perfect for:**
- 🎓 **College Projects** - Meets requirements
- 🚀 **Quick Prototypes** - Fast development
- 👨‍💻 **Learning PHP** - Foundation building
- 📱 **Simple Websites** - Direct functionality

## 🔧 **Development Environment**

### **Required Software:**
1. **XAMPP** or **WAMP** - Local server
2. **PHP 7.4+** - Server language
3. **MySQL 8.0+** - Database
4. **Text Editor** - VS Code, Sublime, etc.

### **Setup Steps:**
1. Install XAMPP
2. Start Apache and MySQL
3. Create database using provided schema
4. Place files in `htdocs` folder
5. Access via `http://localhost/farm2door`

## 📊 **Sample Code Structure**

### **Typical PHP Page:**
```php
<?php
// 1. Include files
require_once 'config/database_simple.php';
require_once 'includes/auth_simple.php';

// 2. Start session
session_start();

// 3. Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form
}

// 4. Get data from database
$products = getAllProducts();

// 5. Display HTML
?>
<!DOCTYPE html>
<html>
<head>
    <title>Page Title</title>
</head>
<body>
    <!-- HTML content -->
</body>
</html>
```

## 🎉 **Conclusion**

**Procedural PHP is perfect for your Farm2Door project!** 

- ✅ **No OOP knowledge required**
- ✅ **Easier to understand and implement**
- ✅ **Faster development time**
- ✅ **Perfect for college projects**
- ✅ **All functionality achievable**

You can build a complete, professional web application using just functions, variables, and basic PHP concepts. The provided code examples show exactly how to implement each feature without any object-oriented programming.

**Start with the database setup, then follow the weekly implementation plan. Each step builds on the previous one, making it easy to learn and implement!**
