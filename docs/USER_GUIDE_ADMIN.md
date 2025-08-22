# Administrator User Guide

## Overview

This guide is designed for Administrator users (Level 1) who have full access to all system features and administrative functions in the CodeIgniter Logistics System. Administrators can manage users, configure system settings, access all modules, and perform system maintenance tasks.

## Getting Started

### Login Process

1. **Access the System**
   - Open your web browser
   - Navigate to the system URL
   - You will see the login page with the company logo

2. **Administrator Login**
   - Username: `adminpuninar`
   - Password: `AdminPuninar123`
   - Click "Login" button or press Enter

3. **Dashboard Overview**
   - After successful login, you'll see the administrator dashboard
   - The dashboard displays system statistics, recent activities, and quick access buttons

### Dashboard Features

The administrator dashboard provides:

- **System Statistics**: Total shipments, active users, pending deliveries
- **Recent Activities**: Latest shipments, user logins, system events
- **Quick Actions**: Create shipment, manage users, view reports
- **System Health**: Database status, file permissions, cache status

## User Management

### Managing System Users

As an administrator, you can manage all system users:

#### Viewing Users
1. Navigate to **Users** → **Manage Users**
2. View list of all system users with their levels and status
3. Use search and filters to find specific users

#### Creating New Users
1. Click **Add New User** button
2. Fill in the required information:
   - **User ID**: Auto-generated (USR format)
   - **Username**: Unique username (3-50 characters)
   - **Password**: Secure password (minimum 6 characters)
   - **User Level**: Select from Admin (1), Finance (2), or Gudang (3)
3. Click **Save** to create the user

#### Editing Users
1. Click the **Edit** button next to a user
2. Modify the user information (cannot change User ID)
3. To change password, enter new password in both fields
4. Click **Update** to save changes

#### User Levels and Permissions

- **Level 1 (Admin)**: Full system access, user management, system configuration
- **Level 2 (Finance)**: Financial reports, customer management, shipment viewing
- **Level 3 (Gudang)**: Inventory management, shipment operations, item categories

### Password Management

#### Changing Your Password
1. Click your username in the top navigation
2. Select **Change Password**
3. Enter current password
4. Enter new password (minimum 6 characters)
5. Confirm new password
6. Click **Update Password**

#### Resetting User Passwords
1. Go to **Users** → **Manage Users**
2. Click **Reset Password** for the target user
3. Enter new password
4. Confirm the password change
5. Notify the user of their new password

## Inventory Management

### Category Management

#### Creating Categories
1. Navigate to **Inventory** → **Categories**
2. Click **Add New Category**
3. Enter category information:
   - **Category ID**: Auto-generated (KTG format)
   - **Name**: Category name (up to 30 characters)
   - **Description**: Optional description (up to 150 characters)
4. Click **Save**

#### Managing Categories
- **Edit**: Click edit button to modify category details
- **Delete**: Remove categories (only if no items are assigned)
- **View Items**: See all items in a specific category

### Item Management

#### Adding New Items
1. Go to **Inventory** → **Items**
2. Click **Add New Item**
3. Fill in item details:
   - **Item ID**: Auto-generated (BRG format)
   - **Name**: Item name (up to 30 characters)
   - **Unit**: Unit of measurement (up to 20 characters)
   - **Delivery Number**: Delivery reference (up to 15 characters)
   - **Category**: Select from existing categories
4. Click **Save**

#### Item Operations
- **Search**: Use search box to find items by name or ID
- **Filter**: Filter items by category
- **Edit**: Modify item information
- **Delete**: Remove items (check for existing shipments first)

## Customer Management

### Managing Customers

#### Adding Customers
1. Navigate to **Customers** → **Manage Customers**
2. Click **Add New Customer**
3. Enter customer information:
   - **Customer ID**: Auto-generated (CST format)
   - **Name**: Customer company name (up to 30 characters)
   - **Phone**: Contact phone number (up to 15 characters)
   - **Address**: Customer address (up to 150 characters)
4. Click **Save**

#### Customer Operations
- **View History**: See all shipments for a customer
- **Edit Details**: Update customer information
- **Generate Reports**: Create customer-specific reports

## Courier Management

### Managing Couriers

#### Adding Couriers
1. Go to **Couriers** → **Manage Couriers**
2. Click **Add New Courier**
3. Fill in courier details:
   - **Courier ID**: Auto-generated (KRR format)
   - **Name**: Courier full name (up to 30 characters)
   - **Gender**: Select gender
   - **Phone**: Contact number (up to 15 characters)
   - **Address**: Courier address (up to 150 characters)
   - **Password**: Login password for courier access
4. Click **Save**

#### Courier Management
- **Performance Tracking**: View delivery statistics
- **Schedule Management**: Assign shipments to couriers
- **Contact Information**: Update courier details

## Shipment Management

### Creating Shipments

#### New Shipment Process
1. Navigate to **Shipments** → **Create Shipment**
2. Enter shipment details:
   - **Shipment ID**: Auto-generated (KRM + date + sequence)
   - **Date**: Shipment date (YYYY-MM-DD format)
   - **Customer**: Select from dropdown
   - **Courier**: Select available courier
   - **Vehicle Number**: Vehicle registration (up to 8 characters)
   - **PO Number**: Purchase order reference (up to 15 characters)
   - **Notes**: Optional additional information
3. Add shipment items:
   - Click **Add Item**
   - Select item from dropdown
   - Enter quantity
   - Repeat for multiple items
4. Click **Create Shipment**

### Managing Shipments

#### Shipment List
- **View All**: See all shipments with filtering options
- **Filter Options**: Date range, status, customer, courier
- **Search**: Find shipments by ID or PO number
- **Export**: Generate Excel or PDF reports

#### Shipment Status Management
- **Pending (0)**: Shipment created, not yet dispatched
- **Delivered (1)**: Successfully delivered to customer
- **Cancelled (2)**: Shipment cancelled

#### Updating Shipment Status
1. Find the shipment in the list
2. Click **Update Status**
3. Select new status
4. For delivered status, add:
   - Recipient name
   - Delivery photo (optional)
   - Delivery notes
5. Click **Update**

### QR Code and Delivery Notes

#### Generating QR Codes
- QR codes are automatically generated for each shipment
- Contains shipment ID and tracking information
- Can be printed on delivery notes

#### Delivery Note Generation
1. Open shipment details
2. Click **Generate Delivery Note**
3. PDF will be created with:
   - Shipment information
   - Item details
   - QR code for tracking
   - Customer and courier information

## Reporting and Analytics

### Available Reports

#### Shipment Reports
1. Go to **Reports** → **Shipment Reports**
2. Select date range
3. Choose filters:
   - Customer
   - Courier
   - Status
   - Item category
4. Select export format (Excel/PDF)
5. Click **Generate Report**

#### Analytics Dashboard
- **Delivery Performance**: Success rates, average delivery times
- **Customer Analytics**: Top customers, shipment volumes
- **Courier Performance**: Delivery statistics, efficiency metrics
- **Inventory Insights**: Most shipped items, category analysis

### Custom Reports

#### Creating Custom Reports
1. Navigate to **Reports** → **Custom Reports**
2. Select data sources
3. Choose fields to include
4. Set filtering criteria
5. Configure grouping and sorting
6. Generate and export report

## System Administration

### System Configuration

#### Application Settings
1. Go to **System** → **Settings**
2. Configure:
   - Company information
   - System timezone
   - Date/time formats
   - Email settings
   - File upload limits

#### Security Settings
- **Password Policy**: Minimum length, complexity requirements
- **Session Timeout**: Automatic logout time
- **Login Attempts**: Maximum failed login attempts
- **IP Restrictions**: Whitelist/blacklist IP addresses

### Database Management

#### Data Backup
1. Navigate to **System** → **Database**
2. Click **Create Backup**
3. Select backup type:
   - Full backup (all data)
   - Incremental backup (changes only)
4. Download backup file

#### Data Migration
- **Import Data**: Upload CSV files for bulk data import
- **Export Data**: Download system data in various formats
- **Data Validation**: Check data integrity and consistency

### System Monitoring

#### Activity Logs
1. Go to **System** → **Activity Logs**
2. View system activities:
   - User logins/logouts
   - Data modifications
   - System errors
   - Security events

#### Performance Monitoring
- **Database Performance**: Query execution times, connection status
- **File System**: Disk usage, file permissions
- **Cache Status**: Cache hit rates, memory usage
- **Error Logs**: System errors and warnings

## Troubleshooting

### Common Issues

#### Login Problems
- **Forgot Password**: Use password reset function
- **Account Locked**: Check failed login attempts, unlock if necessary
- **Session Expired**: Re-login to continue working

#### System Performance
- **Slow Loading**: Check database connections, clear cache
- **File Upload Issues**: Verify file permissions, check upload limits
- **Report Generation**: Ensure sufficient disk space, check database performance

#### Data Issues
- **Missing Data**: Check data migration logs, verify backup integrity
- **Validation Errors**: Review data format requirements
- **Relationship Errors**: Verify foreign key constraints

### Getting Help

#### Support Resources
- **System Documentation**: Complete technical documentation
- **User Forums**: Community support and discussions
- **Help Desk**: Contact system administrators
- **Training Materials**: Video tutorials and guides

#### Emergency Procedures
- **System Downtime**: Contact IT support immediately
- **Data Loss**: Restore from latest backup
- **Security Breach**: Change passwords, review access logs
- **Critical Errors**: Document error messages, contact support

## Best Practices

### Security Best Practices
- Change default passwords immediately
- Use strong, unique passwords
- Log out when finished working
- Don't share login credentials
- Report suspicious activities

### Data Management
- Regular data backups
- Validate data before import
- Keep customer information updated
- Archive old shipment data
- Monitor system performance

### User Management
- Regular user access reviews
- Prompt removal of inactive users
- Proper user level assignments
- Password policy enforcement
- Activity monitoring

This comprehensive guide ensures administrators can effectively manage the logistics system while maintaining security and operational efficiency.