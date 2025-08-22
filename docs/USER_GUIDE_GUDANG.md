# Gudang (Warehouse) User Guide

## Overview

This guide is designed for Gudang users (Level 3) who manage inventory operations, item categories, and shipping operations in the CodeIgniter Logistics System. Gudang users are responsible for day-to-day warehouse operations including inventory management, shipment preparation, and delivery coordination.

## Getting Started

### Login Process

1. **Access the System**
   - Open your web browser
   - Navigate to the system URL
   - You will see the login page

2. **Gudang User Login**
   - Username: `gudangpuninar`
   - Password: `GudangPuninar123`
   - Click "Login" button or press Enter

3. **Warehouse Dashboard**
   - After login, you'll see the warehouse-specific dashboard
   - Dashboard shows inventory levels, pending shipments, and daily operations

### Dashboard Overview

The warehouse dashboard provides:

- **Inventory Summary**: Current stock levels, low stock alerts, item categories
- **Pending Shipments**: Shipments ready for preparation and dispatch
- **Daily Operations**: Today's shipments, completed deliveries, pending tasks
- **Quick Actions**: Create shipment, add items, update inventory, scan QR codes

## Inventory Management

### Managing Item Categories

Gudang users have full control over item categorization for efficient warehouse organization.

#### Viewing Categories
1. Navigate to **Inventory** → **Categories**
2. View all item categories with:
   - Category ID and name
   - Number of items in each category
   - Category descriptions
   - Last modified information

#### Creating New Categories
1. Click **Add New Category**
2. Enter category information:
   - **Category ID**: Auto-generated (KTG format)
   - **Category Name**: Descriptive name (up to 30 characters)
   - **Description**: Detailed description (up to 150 characters)
   - **Storage Location**: Warehouse location for category items
3. Click **Save Category**

#### Managing Categories
- **Edit Categories**: Update category names and descriptions
- **Reorganize**: Move items between categories
- **Delete Categories**: Remove empty categories (no items assigned)
- **Category Reports**: Generate reports by category

### Item Management

#### Adding New Items
1. Go to **Inventory** → **Items**
2. Click **Add New Item**
3. Fill in complete item information:
   - **Item ID**: Auto-generated (BRG format)
   - **Item Name**: Full item description (up to 30 characters)
   - **Unit of Measure**: Piece, box, pallet, etc. (up to 20 characters)
   - **Delivery Number**: Reference number (up to 15 characters)
   - **Category**: Select appropriate category
   - **Storage Location**: Warehouse location
   - **Minimum Stock**: Reorder level
   - **Notes**: Additional item information
4. Click **Save Item**

#### Item Operations
- **Search Items**: Find items by name, ID, or category
- **Edit Items**: Update item information and specifications
- **Stock Levels**: View and update current stock levels
- **Item History**: View shipment history for each item
- **Barcode Generation**: Generate barcodes for items

#### Inventory Tracking
1. **Stock Updates**: Regular stock level updates
2. **Stock Adjustments**: Record stock adjustments with reasons
3. **Low Stock Alerts**: Monitor items below minimum levels
4. **Cycle Counting**: Regular inventory counts and reconciliation

## Shipment Operations

### Creating Shipments

Gudang users are responsible for creating and preparing shipments for delivery.

#### New Shipment Process
1. Navigate to **Shipments** → **Create Shipment**
2. Enter shipment header information:
   - **Shipment Date**: Current or future date
   - **Customer**: Select from customer list
   - **Courier**: Assign available courier
   - **Vehicle Number**: Delivery vehicle registration
   - **PO Number**: Customer purchase order reference
   - **Special Instructions**: Handling or delivery notes

#### Adding Items to Shipment
1. In the shipment form, click **Add Item**
2. Select item from dropdown or scan barcode
3. Enter quantity to ship
4. Verify item details and availability
5. Add multiple items as needed
6. Review total quantities and weights

#### Shipment Validation
- **Stock Availability**: System checks stock levels automatically
- **Item Verification**: Confirm correct items and quantities
- **Customer Verification**: Ensure correct customer and delivery address
- **Documentation**: Verify all required information is complete

### Shipment Preparation

#### Pick List Generation
1. After creating shipment, generate pick list
2. Print pick list for warehouse staff
3. Pick list includes:
   - Item locations in warehouse
   - Quantities to pick
   - Special handling instructions
   - Quality check requirements

#### Packing Operations
1. **Item Picking**: Collect items according to pick list
2. **Quality Check**: Verify item condition and specifications
3. **Quantity Verification**: Confirm picked quantities match requirements
4. **Packing**: Pack items securely for transport
5. **Labeling**: Apply shipment labels and handling instructions

#### Documentation Preparation
1. **Delivery Note**: Generate delivery note with QR code
2. **Packing List**: Detailed list of packed items
3. **Special Instructions**: Include any special handling requirements
4. **Customer Documents**: Include any customer-required documentation

### Shipment Tracking and Updates

#### Shipment Status Management
1. **Prepared**: Shipment ready for pickup
2. **Dispatched**: Shipment picked up by courier
3. **In Transit**: Shipment en route to customer
4. **Delivered**: Shipment delivered to customer
5. **Exception**: Issues requiring attention

#### Updating Shipment Status
1. Find shipment in **Shipments** → **Active Shipments**
2. Click **Update Status**
3. Select new status
4. Add notes or comments
5. Upload delivery confirmation if available
6. Click **Update**

#### QR Code Operations
- **Generate QR Codes**: Automatic generation for each shipment
- **Print QR Codes**: Include on delivery notes and labels
- **Scan QR Codes**: Use mobile device to scan and update status
- **Track Shipments**: Customers can track using QR codes

## Mobile Operations

### Mobile Interface Access

Gudang users can access mobile-optimized features for warehouse operations.

#### Mobile Login
1. Access system URL on mobile device
2. Use same login credentials
3. Mobile interface automatically loads
4. Optimized for touch operations

#### Mobile Features
- **QR Code Scanning**: Scan shipment and item QR codes
- **Status Updates**: Update shipment status on the go
- **Item Lookup**: Quick item information access
- **Photo Capture**: Take delivery confirmation photos

### QR Code Scanning

#### Scanning Shipment QR Codes
1. Open mobile interface
2. Click **Scan QR Code**
3. Point camera at QR code
4. View shipment information
5. Update status if needed

#### Item Barcode Scanning
1. Use **Scan Item** function
2. Scan item barcode or QR code
3. View item details and stock levels
4. Add to shipment if creating new shipment

## Courier Coordination

### Managing Courier Assignments

#### Assigning Couriers to Shipments
1. When creating shipments, select courier
2. Consider courier availability and capacity
3. Check courier location and route efficiency
4. Confirm courier contact information

#### Courier Communication
- **Delivery Instructions**: Provide clear delivery instructions
- **Contact Information**: Ensure courier has customer contact details
- **Special Requirements**: Communicate any special handling needs
- **Route Optimization**: Help plan efficient delivery routes

### Delivery Coordination

#### Scheduling Deliveries
1. **Time Windows**: Coordinate delivery time windows with customers
2. **Route Planning**: Optimize delivery routes for efficiency
3. **Capacity Management**: Ensure courier vehicle capacity is adequate
4. **Priority Handling**: Identify and prioritize urgent deliveries

#### Delivery Confirmation
1. **Photo Confirmation**: Require delivery photos when appropriate
2. **Signature Capture**: Digital signature capture for confirmations
3. **Delivery Notes**: Record any delivery issues or special circumstances
4. **Customer Feedback**: Collect and record customer feedback

## Reporting and Analytics

### Warehouse Reports

#### Daily Operations Reports
1. Navigate to **Reports** → **Daily Reports**
2. Generate reports for:
   - Shipments created and dispatched
   - Items picked and packed
   - Courier performance
   - Inventory movements

#### Inventory Reports
1. Go to **Reports** → **Inventory Reports**
2. Available reports:
   - Current stock levels by category
   - Low stock alerts and reorder recommendations
   - Inventory movement history
   - Stock adjustment reports

#### Performance Reports
- **Shipment Performance**: On-time delivery rates, processing times
- **Picker Performance**: Individual picker productivity and accuracy
- **Courier Performance**: Delivery success rates and customer feedback
- **Quality Metrics**: Error rates and quality control statistics

### Data Export

#### Export Options
1. Select any report or data view
2. Export formats available:
   - **Excel**: For detailed analysis
   - **PDF**: For printed reports
   - **CSV**: For data integration

#### Report Scheduling
- **Daily Reports**: Automatic end-of-day reports
- **Weekly Summaries**: Weekly performance summaries
- **Monthly Reports**: Comprehensive monthly operations reports

## Quality Control

### Quality Assurance Procedures

#### Item Quality Checks
1. **Incoming Inspection**: Check items upon receipt
2. **Storage Conditions**: Maintain proper storage conditions
3. **Pre-Shipment Inspection**: Final quality check before shipment
4. **Damage Reporting**: Document and report any damaged items

#### Process Quality Control
- **Pick Accuracy**: Verify correct items and quantities
- **Packing Standards**: Follow proper packing procedures
- **Documentation Accuracy**: Ensure all paperwork is correct
- **Customer Requirements**: Meet specific customer requirements

### Error Management

#### Handling Errors
1. **Immediate Correction**: Fix errors as soon as discovered
2. **Error Documentation**: Record errors and corrective actions
3. **Root Cause Analysis**: Investigate causes of recurring errors
4. **Process Improvement**: Implement improvements to prevent errors

#### Error Reporting
- **Daily Error Reports**: Track and analyze daily errors
- **Error Trends**: Identify patterns and improvement opportunities
- **Training Needs**: Identify training requirements based on errors
- **Customer Impact**: Assess and minimize customer impact

## Account Management

### Personal Account Settings

#### Changing Password
1. Click your username in the navigation
2. Select **Change Password**
3. Enter current password
4. Enter new password (minimum 6 characters)
5. Confirm new password
6. Click **Update Password**

#### Profile Preferences
- **Display Settings**: Date formats, language preferences
- **Mobile Settings**: Mobile interface preferences
- **Notification Settings**: Alert preferences for low stock, urgent shipments
- **Default Values**: Set default values for common operations

## Safety and Security

### Warehouse Safety

#### Safety Procedures
- **Equipment Safety**: Proper use of warehouse equipment
- **Lifting Procedures**: Safe lifting and handling techniques
- **Emergency Procedures**: Fire, accident, and emergency response
- **Personal Protective Equipment**: Required PPE for warehouse operations

#### Security Measures
- **Access Control**: Secure access to warehouse areas
- **Inventory Security**: Protect inventory from theft and damage
- **Data Security**: Protect customer and shipment information
- **System Security**: Secure login and logout procedures

### Data Protection

#### Information Security
- **Customer Data**: Protect customer information and delivery details
- **Shipment Information**: Secure handling of shipment data
- **System Access**: Proper use of system access privileges
- **Mobile Security**: Secure use of mobile devices for system access

## Troubleshooting

### Common Issues

#### Inventory Problems
- **Stock Discrepancies**: Investigate and resolve stock count differences
- **Item Not Found**: Verify item codes and locations
- **Low Stock**: Coordinate with purchasing for reorders
- **Damaged Items**: Document damage and arrange replacements

#### Shipment Issues
- **Missing Items**: Investigate and resolve missing item issues
- **Wrong Quantities**: Correct quantity errors and update records
- **Delivery Problems**: Coordinate with couriers to resolve delivery issues
- **Customer Complaints**: Address customer concerns promptly

#### System Issues
- **Login Problems**: Verify credentials and contact support if needed
- **Mobile Access**: Troubleshoot mobile connectivity and app issues
- **QR Code Scanning**: Ensure proper lighting and camera focus
- **Report Generation**: Check filters and date ranges for reports

### Getting Help

#### Support Resources
- **Warehouse Supervisor**: First point of contact for operational issues
- **System Support**: Technical support for system-related problems
- **Training Materials**: Access to training videos and documentation
- **User Manual**: Complete user guide for reference

#### Emergency Procedures
- **System Downtime**: Manual procedures for system outages
- **Urgent Shipments**: Expedited processing procedures
- **Safety Incidents**: Emergency response and reporting procedures
- **Security Issues**: Immediate reporting and response procedures

## Best Practices

### Operational Excellence
- **Accuracy First**: Double-check all information and quantities
- **Efficiency**: Optimize workflows and minimize waste
- **Communication**: Maintain clear communication with all stakeholders
- **Continuous Improvement**: Suggest and implement process improvements

### Inventory Management
- **Regular Counts**: Perform regular inventory counts and reconciliation
- **Proper Storage**: Maintain proper storage conditions and organization
- **FIFO Procedures**: First-in, first-out inventory rotation
- **Documentation**: Maintain accurate and timely documentation

### Customer Service
- **Quality Focus**: Ensure all shipments meet quality standards
- **Timely Processing**: Process shipments promptly and efficiently
- **Problem Resolution**: Address issues quickly and professionally
- **Customer Communication**: Keep customers informed of shipment status

This comprehensive guide ensures gudang users can effectively manage warehouse operations, maintain inventory accuracy, and provide excellent customer service while following proper safety and security procedures.