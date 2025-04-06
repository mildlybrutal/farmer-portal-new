# Farmer's Portal

A simple web application connecting farmers directly with retailers, featuring product listings, bidding system, and order management.

## Features

- **For Farmers:**
  - List products with details and images
  - Receive and manage bids from retailers
  - Accept/reject bids
  - View order history

- **For Retailers:**
  - Browse available products
  - Place bids on products
  - View bid status
  - Track orders

## Tech Stack

- **Frontend:** HTML, Tailwind CSS, Vanilla JavaScript
- **Backend:** Core PHP
- **Database:** MySQL
- **Authentication:** Session-based

## Setup Instructions

### 1. XAMPP Setup (Windows)

1. **Install XAMPP**
   - Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - During installation, select Apache, MySQL, and PHP
   - Complete the installation

2. **Start Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services
   - Ensure both services are running

3. **Project Setup**
   - Copy the entire project folder to `C:\xampp\htdocs\farmer-portal`
   - Access the application at `http://localhost/farmer-portal`

4. **Database Setup**
   - Open browser and go to `http://localhost/phpmyadmin`
   - Click "New" to create a new database
   - Enter database name: `farmers_portal`
   - Click "Create"
   - After creation, click "Import"
   - Select the `database.sql` file from your project folder
   - Click "Go" to import the schema

5. **File Permissions**
   - Navigate to `C:\xampp\htdocs\farmer-portal\uploads`
   - Right-click on the `uploads` folder
   - Select "Properties"
   - Go to "Security" tab
   - Click "Edit"
   - Add "IUSR" user and give it "Modify" permissions
   - Click "Apply" and "OK"

### 2. Ubuntu Setup (Without XAMPP)

1. **Install Required Packages**
   ```bash
   # Update package list
   sudo apt update

   # Install Apache
   sudo apt install apache2

   # Install MySQL
   sudo apt install mysql-server

   # Install PHP and required extensions
   sudo apt install php libapache2-mod-php php-mysql php-gd
   ```

2. **Configure MySQL**
   ```bash
   # Secure MySQL installation
   sudo mysql_secure_installation

   # Create database and user
   sudo mysql -e "CREATE DATABASE farmers_portal;"
   sudo mysql -e "CREATE USER 'farmer'@'localhost' IDENTIFIED BY 'farmer123';"
   sudo mysql -e "GRANT ALL PRIVILEGES ON farmers_portal.* TO 'farmer'@'localhost';"
   sudo mysql -e "FLUSH PRIVILEGES;"
   ```

3. **Configure Apache**
   ```bash
   # Enable required Apache modules
   sudo a2enmod rewrite
   sudo systemctl restart apache2

   # Copy project to web root
   sudo cp -r /path/to/project/* /var/www/html/farmer-portal/

   # Set correct permissions
   sudo chown -R www-data:www-data /var/www/html/farmer-portal
   sudo chmod -R 755 /var/www/html/farmer-portal
   ```

4. **Import Database**
   ```bash
   # Import database schema
   sudo mysql farmers_portal < /var/www/html/farmer-portal/database.sql
   ```

5. **Update Database Configuration**
   - Open `/var/www/html/farmer-portal/config/database.php`
   - Update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'farmer');
     define('DB_PASS', 'farmer123');
     define('DB_NAME', 'farmers_portal');
     ```

### 3. Common Configuration

1. **File Upload Directory**
   - Ensure the `uploads/products` directory exists and is writable
   ```bash
   mkdir -p uploads/products
   chmod -R 755 uploads
   ```

2. **PHP Configuration**
   - Edit `php.ini` to adjust file upload limits:
     ```ini
     upload_max_filesize = 10M
     post_max_size = 10M
     max_execution_time = 300
     ```

3. **Access the Application**
   - XAMPP: `http://localhost/farmer-portal`
   - Ubuntu: `http://localhost/farmer-portal`

## Project Structure

```
/
├── assets/           # CSS, JS, images
├── auth/            # Login/registration
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── config/          # Configuration files
│   └── database.php
├── farmer/          # Farmer features
│   ├── dashboard.php
│   ├── add_product.php
│   └── handle_bid.php
├── retailer/        # Retailer features
│   ├── dashboard.php
│   ├── place_bid.php
│   └── orders.php
├── includes/        # Shared components
│   └── auth_middleware.php
├── uploads/         # User uploads
│   └── products/    # Product images
└── database.sql     # Database schema
```

## Usage

1. **For Farmers:**
   - Register as a farmer
   - Add products with details and images
   - View and manage incoming bids
   - Accept/reject bids to generate orders

2. **For Retailers:**
   - Register as a retailer
   - Browse available products
   - Place bids with custom amounts
   - Track orders and bid status

## Security Features

- Password hashing
- SQL injection prevention
- Session-based authentication
- Input validation and sanitization
- Secure file upload handling

## Troubleshooting

1. **Database Connection Issues**
   - Verify MySQL service is running
   - Check database credentials in `config/database.php`
   - Ensure database exists and has proper permissions

2. **File Upload Issues**
   - Check directory permissions
   - Verify PHP upload limits in `php.ini`
   - Ensure proper file permissions on upload directory

3. **Web Server Issues**
   - Check Apache error logs
   - Verify virtual host configuration
   - Ensure proper permissions on web root directory

## Support

If you encounter any issues, please check:
1. Error logs
2. Browser console
3. Apache error logs (Ubuntu: `/var/log/apache2/error.log`)
4. MySQL error logs
