# Business Processes and Workflows

## Overview

This document outlines the business processes and workflows implemented in the CodeIgniter Logistics System. It provides detailed descriptions of how business operations flow through the system, from initial setup through daily operations and reporting.

## Core Business Processes

### 1. System Initialization Process

#### Initial System Setup
```mermaid
flowchart TD
    A[System Installation] --> B[Database Setup]
    B --> C[Run Migrations]
    C --> D[Execute Seeders]
    D --> E[Create Default Users]
    E --> F[Configure System Settings]
    F --> G[System Ready]
```

**Process Steps:**
1. **Database Creation**: Create MySQL database with proper character set
2. **Migration Execution**: Run all database migrations to create tables
3. **Data Seeding**: Populate initial data including default users
4. **User Account Setup**: Create three default user accounts with proper credentials
5. **System Configuration**: Configure application settings and security parameters

**Stakeholders:**
- System Administrator
- IT Support Team
- Database Administrator

**Success Criteria:**
- All database tables created successfully
- Default users can log in with specified credentials
- System passes health checks

### 2. User Management Workflow

#### User Creation Process
```mermaid
flowchart TD
    A[Admin Login] --> B[Navigate to User Management]
    B --> C[Click Add New User]
    C --> D[Fill User Information]
    D --> E[Select User Level]
    E --> F[Set Password]
    F --> G[Save User]
    G --> H{Validation Success?}
    H -->|Yes| I[User Created]
    H -->|No| J[Show Errors]
    J --> D
    I --> K[Send Credentials to User]
```

**Process Details:**
- **Input Requirements**: Username, password, user level (1-3)
- **Validation Rules**: Unique username, strong password, valid user level
- **Security Measures**: Password hashing, input sanitization
- **Notifications**: User credentials communicated securely

**User Levels and Permissions:**
- **Level 1 (Admin)**: Full system access, user management, configuration
- **Level 2 (Finance)**: Financial reports, customer management, read-only inventory
- **Level 3 (Gudang)**: Inventory management, shipment operations, item categories

### 3. Inventory Management Process

#### Category Management Workflow
```mermaid
flowchart TD
    A[Gudang User Login] --> B[Navigate to Categories]
    B --> C[View Category List]
    C --> D{Action Required?}
    D -->|Add New| E[Create Category Form]
    D -->|Edit Existing| F[Edit Category Form]
    D -->|Delete| G[Confirm Deletion]
    D -->|View Items| H[Show Category Items]
    E --> I[Enter Category Details]
    F --> I
    I --> J[Save Category]
    J --> K{Validation Success?}
    K -->|Yes| L[Category Saved]
    K -->|No| M[Show Errors]
    M --> I
    G --> N{Has Items?}
    N -->|Yes| O[Cannot Delete - Show Error]
    N -->|No| P[Delete Category]
```

#### Item Management Workflow
```mermaid
flowchart TD
    A[Access Item Management] --> B[View Item List]
    B --> C{Action Required?}
    C -->|Add Item| D[Create Item Form]
    C -->|Edit Item| E[Edit Item Form]
    C -->|Search| F[Apply Search Filters]
    C -->|View Details| G[Show Item Details]
    D --> H[Enter Item Information]
    E --> H
    H --> I[Select Category]
    I --> J[Save Item]
    J --> K{Validation Success?}
    K -->|Yes| L[Item Saved]
    K -->|No| M[Show Errors]
    M --> H
    F --> N[Display Filtered Results]
    G --> O[Show Item History]
```

**Business Rules:**
- Items must belong to a valid category
- Item IDs are auto-generated in BRG format
- Categories cannot be deleted if they contain items
- Item names must be unique within categories

### 4. Customer Management Process

#### Customer Registration Workflow
```mermaid
flowchart TD
    A[Finance/Admin User] --> B[Navigate to Customers]
    B --> C[Click Add Customer]
    C --> D[Enter Customer Information]
    D --> E[Validate Information]
    E --> F{Validation Pass?}
    F -->|Yes| G[Save Customer]
    F -->|No| H[Show Validation Errors]
    H --> D
    G --> I[Generate Customer ID]
    I --> J[Customer Created]
    J --> K[Update Customer List]
```

**Required Information:**
- Company name (up to 30 characters)
- Contact phone number (Indonesian format)
- Complete business address
- Billing information (for finance users)

**Validation Rules:**
- Unique customer names within the system
- Valid phone number format
- Complete address information required
- Customer ID auto-generated in CST format

### 5. Courier Management Process

#### Courier Registration and Management
```mermaid
flowchart TD
    A[Admin/Gudang User] --> B[Access Courier Management]
    B --> C[Add New Courier]
    C --> D[Enter Courier Details]
    D --> E[Set Courier Password]
    E --> F[Assign Courier ID]
    F --> G[Save Courier Information]
    G --> H{Validation Success?}
    H -->|Yes| I[Courier Registered]
    H -->|No| J[Show Errors]
    J --> D
    I --> K[Courier Available for Assignment]
```

**Courier Information:**
- Personal details (name, gender, contact)
- Address and location information
- Login credentials for system access
- Performance tracking metrics

### 6. Shipment Management Process

#### Shipment Creation Workflow
```mermaid
flowchart TD
    A[Gudang User Login] --> B[Navigate to Shipments]
    B --> C[Click Create Shipment]
    C --> D[Enter Shipment Header]
    D --> E[Select Customer]
    E --> F[Select Courier]
    F --> G[Enter Vehicle Details]
    G --> H[Add Shipment Items]
    H --> I[Select Item]
    I --> J[Enter Quantity]
    J --> K{Add More Items?}
    K -->|Yes| H
    K -->|No| L[Review Shipment]
    L --> M[Validate Data]
    M --> N{Validation Pass?}
    N -->|Yes| O[Generate Shipment ID]
    N -->|No| P[Show Errors]
    P --> D
    O --> Q[Save Shipment]
    Q --> R[Generate QR Code]
    R --> S[Create Delivery Note]
    S --> T[Shipment Created]
```

**Shipment Creation Rules:**
- Shipment ID format: KRM + YYYYMMDD + sequence number
- All items must be available in inventory
- Customer and courier must be valid and active
- Vehicle number must be provided
- Purchase order number is required

#### Shipment Status Management
```mermaid
flowchart TD
    A[Shipment Created] --> B[Status: Pending]
    B --> C{Courier Action}
    C -->|Pick Up| D[Status: Dispatched]
    C -->|Cancel| E[Status: Cancelled]
    D --> F{Delivery Action}
    F -->|Deliver| G[Status: Delivered]
    F -->|Return| H[Status: Returned]
    F -->|Exception| I[Status: Exception]
    G --> J[Capture Delivery Proof]
    J --> K[Update Delivery Information]
    K --> L[Shipment Complete]
```

**Status Definitions:**
- **Pending (0)**: Shipment created, awaiting dispatch
- **Delivered (1)**: Successfully delivered to customer
- **Cancelled (2)**: Shipment cancelled before delivery

#### Delivery Confirmation Process
```mermaid
flowchart TD
    A[Courier Arrives at Destination] --> B[Scan QR Code]
    B --> C[Verify Shipment Details]
    C --> D[Confirm Delivery]
    D --> E[Capture Recipient Name]
    E --> F[Take Delivery Photo]
    F --> G[Add Delivery Notes]
    G --> H[Update Status to Delivered]
    H --> I[Generate Delivery Confirmation]
    I --> J[Notify Stakeholders]
```

### 7. QR Code and Document Generation

#### QR Code Generation Process
```mermaid
flowchart TD
    A[Shipment Created] --> B[Generate Unique QR Data]
    B --> C[Create QR Code Image]
    C --> D[Embed in Delivery Note]
    D --> E[Store QR Code Reference]
    E --> F[QR Code Ready for Use]
    
    G[Mobile Scan] --> H[Decode QR Data]
    H --> I[Retrieve Shipment Info]
    I --> J[Display Shipment Details]
    J --> K[Allow Status Update]
```

**QR Code Content:**
- Shipment ID
- Creation timestamp
- Verification hash
- Customer reference

#### Delivery Note Generation
```mermaid
flowchart TD
    A[Request Delivery Note] --> B[Gather Shipment Data]
    B --> C[Collect Item Details]
    C --> D[Get Customer Information]
    D --> E[Get Courier Information]
    E --> F[Generate QR Code]
    F --> G[Create PDF Document]
    G --> H[Include Company Branding]
    H --> I[Add Terms and Conditions]
    I --> J[Delivery Note Complete]
```

### 8. Reporting and Analytics Process

#### Report Generation Workflow
```mermaid
flowchart TD
    A[User Request Report] --> B[Select Report Type]
    B --> C[Set Date Range]
    C --> D[Apply Filters]
    D --> E[Choose Export Format]
    E --> F[Generate Report]
    F --> G{Data Available?}
    G -->|Yes| H[Process Data]
    G -->|No| I[Show No Data Message]
    H --> J[Format Output]
    J --> K[Export File]
    K --> L[Provide Download Link]
```

**Available Reports:**
- Daily shipment summaries
- Monthly performance reports
- Customer analysis reports
- Courier performance metrics
- Inventory movement reports
- Financial summaries (Finance users)

#### Analytics Dashboard Process
```mermaid
flowchart TD
    A[User Access Dashboard] --> B[Load User Role]
    B --> C{User Level?}
    C -->|Admin| D[Full Analytics Dashboard]
    C -->|Finance| E[Financial Analytics]
    C -->|Gudang| F[Operations Analytics]
    D --> G[System Statistics]
    E --> H[Revenue Metrics]
    F --> I[Inventory Metrics]
    G --> J[Recent Activities]
    H --> J
    I --> J
    J --> K[Performance Charts]
    K --> L[Dashboard Complete]
```

### 9. Security and Access Control Process

#### Authentication Workflow
```mermaid
flowchart TD
    A[User Access System] --> B[Enter Credentials]
    B --> C[Validate Username]
    C --> D{Username Valid?}
    D -->|No| E[Show Error Message]
    D -->|Yes| F[Verify Password]
    F --> G{Password Correct?}
    G -->|No| H[Increment Failed Attempts]
    G -->|Yes| I[Create Session]
    H --> J{Max Attempts Reached?}
    J -->|Yes| K[Lock Account]
    J -->|No| E
    I --> L[Load User Permissions]
    L --> M[Redirect to Dashboard]
    E --> N[Return to Login]
    K --> N
```

#### Authorization Process
```mermaid
flowchart TD
    A[User Request Resource] --> B[Check Authentication]
    B --> C{User Logged In?}
    C -->|No| D[Redirect to Login]
    C -->|Yes| E[Check User Level]
    E --> F{Permission Granted?}
    F -->|No| G[Show Access Denied]
    F -->|Yes| H[Allow Access]
    H --> I[Log Activity]
    I --> J[Serve Resource]
```

### 10. Data Migration and Integration Process

#### Legacy Data Migration
```mermaid
flowchart TD
    A[Backup Current System] --> B[Analyze Legacy Data]
    B --> C[Create Migration Scripts]
    C --> D[Validate Data Integrity]
    D --> E[Execute Migration]
    E --> F[Verify Migration Results]
    F --> G{Migration Successful?}
    G -->|No| H[Rollback Changes]
    G -->|Yes| I[Update User Credentials]
    H --> J[Fix Issues]
    J --> E
    I --> K[Test System Functions]
    K --> L[Migration Complete]
```

**Migration Steps:**
1. **Data Backup**: Complete backup of existing system
2. **Schema Mapping**: Map old schema to new structure
3. **Data Transformation**: Convert data to new format
4. **Validation**: Verify data integrity and completeness
5. **User Update**: Update user credentials and permissions
6. **Testing**: Comprehensive system testing

### 11. Mobile Operations Process

#### Mobile QR Scanning Workflow
```mermaid
flowchart TD
    A[Courier Opens Mobile App] --> B[Login with Credentials]
    B --> C[Access QR Scanner]
    C --> D[Scan Shipment QR Code]
    D --> E[Decode QR Data]
    E --> F[Retrieve Shipment Info]
    F --> G[Display Shipment Details]
    G --> H{Update Status?}
    H -->|Yes| I[Select New Status]
    H -->|No| J[View Only Mode]
    I --> K[Add Status Notes]
    K --> L[Capture Photo if Needed]
    L --> M[Submit Status Update]
    M --> N[Sync with Server]
    N --> O[Confirm Update]
```

### 12. Error Handling and Recovery Process

#### System Error Management
```mermaid
flowchart TD
    A[Error Detected] --> B[Log Error Details]
    B --> C[Classify Error Severity]
    C --> D{Critical Error?}
    D -->|Yes| E[Alert Administrators]
    D -->|No| F[Standard Error Handling]
    E --> G[Immediate Response Required]
    F --> H[Show User-Friendly Message]
    G --> I[Investigate and Fix]
    H --> J[Log for Review]
    I --> K[System Recovery]
    J --> L[Continue Operation]
    K --> L
```

**Error Categories:**
- **Critical**: System failures, security breaches
- **High**: Data corruption, authentication failures
- **Medium**: Validation errors, performance issues
- **Low**: User input errors, minor display issues

### 13. Backup and Recovery Process

#### Automated Backup Workflow
```mermaid
flowchart TD
    A[Scheduled Backup Time] --> B[Check System Status]
    B --> C[Create Database Backup]
    C --> D[Backup File System]
    D --> E[Verify Backup Integrity]
    E --> F{Backup Valid?}
    F -->|Yes| G[Store Backup Securely]
    F -->|No| H[Retry Backup]
    G --> I[Update Backup Log]
    H --> J{Retry Count < 3?}
    J -->|Yes| C
    J -->|No| K[Alert Administrators]
    I --> L[Cleanup Old Backups]
    K --> L
```

**Backup Schedule:**
- **Daily**: Incremental database backups
- **Weekly**: Full system backups
- **Monthly**: Archive backups for long-term storage

### 14. Performance Monitoring Process

#### System Performance Monitoring
```mermaid
flowchart TD
    A[Monitor System Metrics] --> B[Check Database Performance]
    B --> C[Monitor Response Times]
    C --> D[Check Resource Usage]
    D --> E{Performance Issues?}
    E -->|No| F[Continue Monitoring]
    E -->|Yes| G[Identify Bottlenecks]
    G --> H[Apply Optimizations]
    H --> I[Verify Improvements]
    I --> J{Performance Restored?}
    J -->|Yes| F
    J -->|No| K[Escalate to Administrators]
```

**Monitoring Metrics:**
- Database query performance
- Page load times
- Memory and CPU usage
- Disk space utilization
- Network latency

## Business Rules and Constraints

### Data Integrity Rules
1. **Referential Integrity**: All foreign key relationships must be maintained
2. **Unique Constraints**: Usernames, shipment IDs, and customer names must be unique
3. **Data Validation**: All input data must pass validation before storage
4. **Audit Trail**: All data modifications must be logged with user and timestamp

### Business Logic Constraints
1. **User Permissions**: Users can only access functions appropriate to their level
2. **Shipment Status**: Status changes must follow logical progression
3. **Inventory Management**: Items cannot be deleted if referenced in shipments
4. **Customer Management**: Customer information must be complete before creating shipments

### Security Requirements
1. **Authentication**: All users must authenticate before system access
2. **Password Policy**: Passwords must meet minimum security requirements
3. **Session Management**: Sessions must timeout after inactivity
4. **Data Protection**: Sensitive data must be encrypted and protected

This comprehensive business process documentation ensures that all stakeholders understand how the logistics system operates and how business workflows are implemented within the technical architecture.