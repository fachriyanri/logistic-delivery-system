# Design Document

## Overview

This design document outlines the modernization of the existing CodeIgniter 2.2.0 logistics application to CodeIgniter 4.x with PHP 8.0.6 compatibility. The system will maintain all existing functionality while implementing modern architecture patterns, enhanced security, improved UI/UX, and comprehensive documentation. The application manages logistics operations including inventory, shipping, customer management, and reporting with a three-tier user access system.

## Architecture

### Framework Migration Strategy

**CodeIgniter 4.x Architecture**
- **Namespace Support**: Implement PSR-4 autoloading with proper namespacing
- **Dependency Injection**: Use CodeIgniter 4's service container for better testability
- **Entity/Model Pattern**: Replace active record with Entity-Model pattern for better data handling
- **Request/Response Objects**: Implement proper HTTP request/response handling
- **Filters**: Replace hooks with modern filter system for authentication and authorization

**PHP 8.0 Compatibility**
- **Type Declarations**: Add strict typing throughout the application
- **Constructor Property Promotion**: Utilize PHP 8.0 constructor features
- **Match Expressions**: Replace switch statements with match expressions where appropriate
- **Null Safe Operator**: Implement null-safe operations for better error handling
- **Named Arguments**: Use named arguments for better code readability

### Database Layer Modernization

**Migration from CI 2.2 to CI 4.x Database**
- **Query Builder Enhancement**: Migrate from Active Record to modern Query Builder
- **Database Migrations**: Implement proper migration system for schema management
- **Database Seeding**: Create seeders for initial data and test data
- **Connection Management**: Implement proper connection pooling and management

**Security Enhancements**
- **Prepared Statements**: Ensure all queries use prepared statements
- **Input Validation**: Implement comprehensive input validation and sanitization
- **CSRF Protection**: Enable and configure CSRF protection
- **SQL Injection Prevention**: Use parameter binding for all database operations

## Components and Interfaces

### Authentication and Authorization System

**User Management Component**
```php
namespace App\Controllers;

class AuthController extends BaseController
{
    protected UserService $userService;
    protected AuthenticationService $authService;
    
    public function login(): ResponseInterface
    public function logout(): ResponseInterface
    public function authenticate(): ResponseInterface
}

class UserService
{
    public function authenticateUser(string $username, string $password): ?UserEntity
    public function createUser(array $userData): UserEntity
    public function updatePassword(int $userId, string $newPassword): bool
}
```

**Role-Based Access Control**
- **Admin Level (1)**: Full system access, user management, system configuration
- **Finance Level (2)**: Financial reports, shipping records, customer management, read-only inventory
- **Gudang Level (3)**: Inventory management, shipping operations, item categories

**Authorization Filter**
```php
namespace App\Filters;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
}

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
}
```

### Core Business Logic Components

**Inventory Management**
```php
namespace App\Controllers;

class KategoriController extends BaseController
{
    protected KategoriService $kategoriService;
    
    public function index(): string
    public function create(): string
    public function store(): ResponseInterface
    public function edit(string $id): string
    public function update(string $id): ResponseInterface
    public function delete(string $id): ResponseInterface
}

class BarangController extends BaseController
{
    protected BarangService $barangService;
    protected KategoriService $kategoriService;
}
```

**Shipping Management**
```php
namespace App\Controllers;

class PengirimanController extends BaseController
{
    protected PengirimanService $pengirimanService;
    protected QRCodeService $qrCodeService;
    
    public function index(): string
    public function create(): string
    public function store(): ResponseInterface
    public function generateDeliveryNote(string $id): ResponseInterface
    public function updateStatus(string $id): ResponseInterface
}
```

**Customer and Courier Management**
```php
namespace App\Controllers;

class PelangganController extends BaseController
{
    protected PelangganService $pelangganService;
}

class KurirController extends BaseController
{
    protected KurirService $kurirService;
}
```

### Service Layer Architecture

**Business Logic Services**
```php
namespace App\Services;

interface PengirimanServiceInterface
{
    public function createShipment(array $shipmentData): PengirimanEntity;
    public function updateShipmentStatus(string $id, int $status, array $additionalData = []): bool;
    public function generateShipmentReport(array $filters): array;
    public function getShipmentsByDateRange(string $from, string $to): array;
}

class PengirimanService implements PengirimanServiceInterface
{
    protected PengirimanModel $pengirimanModel;
    protected DetailPengirimanModel $detailModel;
    protected QRCodeService $qrCodeService;
}
```

**Utility Services**
```php
namespace App\Services;

class QRCodeService
{
    public function generateQRCode(string $data, string $filename = null): string;
    public function validateQRCode(string $qrData): bool;
}

class ExportService
{
    public function exportToExcel(array $data, string $filename): string;
    public function exportToPDF(array $data, string $template): string;
}

class ValidationService
{
    public function validateShipmentData(array $data): array;
    public function validateUserData(array $data): array;
}
```

## Data Models

### Entity Classes

**User Entity**
```php
namespace App\Entities;

class UserEntity extends Entity
{
    protected $attributes = [
        'id_user' => null,
        'username' => null,
        'password' => null,
        'level' => null,
        'created_at' => null,
        'updated_at' => null,
    ];
    
    protected $casts = [
        'level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function setPassword(string $password): self;
    public function verifyPassword(string $password): bool;
    public function hasRole(string $role): bool;
}
```

**Pengiriman Entity**
```php
namespace App\Entities;

class PengirimanEntity extends Entity
{
    protected $attributes = [
        'id_pengiriman' => null,
        'tanggal' => null,
        'id_pelanggan' => null,
        'id_kurir' => null,
        'no_kendaraan' => null,
        'no_po' => null,
        'keterangan' => null,
        'penerima' => null,
        'photo' => null,
        'status' => null,
    ];
    
    protected $casts = [
        'tanggal' => 'datetime',
        'status' => 'integer',
    ];
    
    public function getStatusText(): string;
    public function isDelivered(): bool;
    public function canBeModified(): bool;
}
```

### Model Classes

**Enhanced Model Architecture**
```php
namespace App\Models;

class PengirimanModel extends Model
{
    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';
    protected $returnType = PengirimanEntity::class;
    protected $allowedFields = [
        'id_pengiriman', 'tanggal', 'id_pelanggan', 'id_kurir',
        'no_kendaraan', 'no_po', 'keterangan', 'penerima', 'photo', 'status'
    ];
    
    protected $validationRules = [
        'id_pengiriman' => 'required|is_unique[pengiriman.id_pengiriman,id_pengiriman,{id_pengiriman}]',
        'tanggal' => 'required|valid_date',
        'id_pelanggan' => 'required|is_not_unique[pelanggan.id_pelanggan]',
        'id_kurir' => 'required|is_not_unique[kurir.id_kurir]',
    ];
    
    public function getWithDetails(string $id): ?PengirimanEntity;
    public function getShipmentsByDateRange(string $from, string $to, array $filters = []): array;
    public function generateNextId(): string;
}
```

### Database Schema Enhancements

**Migration Files**
```php
// Migration: 001_modernize_user_table.php
class ModernizeUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user' => ['type' => 'VARCHAR', 'constraint' => 5],
            'username' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'level' => ['type' => 'TINYINT', 'constraint' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_user');
        $this->forge->createTable('user');
    }
}
```

## Error Handling

### Exception Management

**Custom Exception Classes**
```php
namespace App\Exceptions;

class AuthenticationException extends \Exception
{
    protected $message = 'Authentication failed';
}

class AuthorizationException extends \Exception
{
    protected $message = 'Insufficient permissions';
}

class ValidationException extends \Exception
{
    protected array $errors;
    
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation failed');
    }
}
```

**Global Error Handler**
```php
namespace App\Controllers;

class ErrorController extends BaseController
{
    public function show404(): string;
    public function show500(): string;
    public function showAccessDenied(): string;
}
```

### Logging Strategy

**Structured Logging**
- **Authentication Events**: Login attempts, password changes, access violations
- **Business Operations**: Shipment creation, status updates, data modifications
- **System Events**: Database errors, file operations, external service calls
- **Security Events**: Failed authentication, unauthorized access attempts

## Testing Strategy

### Unit Testing

**Model Testing**
```php
namespace Tests\Unit\Models;

class PengirimanModelTest extends CIUnitTestCase
{
    public function testCreateShipment(): void;
    public function testUpdateShipmentStatus(): void;
    public function testGenerateNextId(): void;
    public function testValidationRules(): void;
}
```

**Service Testing**
```php
namespace Tests\Unit\Services;

class PengirimanServiceTest extends CIUnitTestCase
{
    public function testCreateShipmentWithValidData(): void;
    public function testCreateShipmentWithInvalidData(): void;
    public function testGenerateDeliveryReport(): void;
}
```

### Integration Testing

**Controller Testing**
```php
namespace Tests\Feature\Controllers;

class PengirimanControllerTest extends FeatureTestCase
{
    public function testIndexPageRequiresAuthentication(): void;
    public function testCreateShipmentWithValidData(): void;
    public function testUpdateShipmentStatus(): void;
    public function testGenerateDeliveryNote(): void;
}
```

**Database Testing**
- **Migration Testing**: Verify all migrations run successfully
- **Seeder Testing**: Ensure seeders populate correct data
- **Relationship Testing**: Verify foreign key constraints and relationships

### End-to-End Testing

**User Workflow Testing**
- **Authentication Flow**: Login, logout, password change
- **Shipment Management**: Create, update, track shipments
- **Report Generation**: Generate and export reports
- **Role-Based Access**: Verify access controls work correctly

## User Interface Design

### Modern UI Framework

**Frontend Technology Stack**
- **CSS Framework**: Bootstrap 5.x for responsive design
- **JavaScript**: Vanilla JavaScript with modern ES6+ features
- **Icons**: Font Awesome 6.x for consistent iconography
- **Charts**: Chart.js for dashboard analytics
- **DataTables**: Enhanced table functionality with sorting, filtering, pagination

### Design System

**Color Palette** (Based on Yusen Logistics reference)
- **Primary**: Dark navy (#1a2332)
- **Secondary**: Orange accent (#ff6b35)
- **Success**: Green (#28a745)
- **Warning**: Yellow (#ffc107)
- **Danger**: Red (#dc3545)
- **Light**: Light gray (#f8f9fa)
- **Dark**: Dark gray (#343a40)

**Typography**
- **Primary Font**: Inter or system fonts for better performance
- **Headings**: Bold weights for hierarchy
- **Body Text**: Regular weight, optimized line height for readability

**Layout Structure**
```html
<!-- Modern Layout Template -->
<div class="app-wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <!-- Navigation with company logo and user menu -->
    </nav>
    
    <div class="app-content">
        <aside class="sidebar">
            <!-- Role-based navigation menu -->
        </aside>
        
        <main class="main-content">
            <!-- Page content with breadcrumbs -->
        </main>
    </div>
</div>
```

### Responsive Design Patterns

**Mobile-First Approach**
- **Breakpoints**: 576px (sm), 768px (md), 992px (lg), 1200px (xl)
- **Navigation**: Collapsible sidebar for mobile devices
- **Tables**: Horizontal scrolling and card layout for mobile
- **Forms**: Stacked layout on mobile, inline on desktop

**Component Library**
```php
// View Components
namespace App\Views\Components;

class DataTable extends Component
{
    public function render(): string;
}

class FormCard extends Component
{
    public function render(): string;
}

class StatusBadge extends Component
{
    public function render(): string;
}
```

### User Experience Enhancements

**Interactive Features**
- **Real-time Validation**: Client-side form validation with server-side confirmation
- **Progressive Enhancement**: Core functionality works without JavaScript
- **Loading States**: Skeleton screens and progress indicators
- **Toast Notifications**: Non-intrusive success/error messages

**Accessibility Features**
- **ARIA Labels**: Proper labeling for screen readers
- **Keyboard Navigation**: Full keyboard accessibility
- **Color Contrast**: WCAG 2.1 AA compliance
- **Focus Management**: Logical tab order and focus indicators

## Security Implementation

### Authentication Security

**Password Security**
```php
namespace App\Services;

class PasswordService
{
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
    
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
```

**Session Security**
- **Secure Cookies**: HTTPOnly, Secure, SameSite attributes
- **Session Regeneration**: Regenerate session ID on login
- **Session Timeout**: Automatic logout after inactivity
- **CSRF Protection**: Token-based CSRF protection

### Input Validation and Sanitization

**Validation Rules**
```php
namespace App\Validation;

class CustomRules
{
    public function valid_shipment_id(string $str): bool;
    public function valid_phone_number(string $str): bool;
    public function valid_vehicle_number(string $str): bool;
}
```

**XSS Prevention**
- **Output Escaping**: Automatic escaping in views
- **Content Security Policy**: Strict CSP headers
- **Input Filtering**: Whitelist-based input filtering

### File Upload Security

**Secure File Handling**
```php
namespace App\Services;

class FileUploadService
{
    public function validateFile(UploadedFile $file): bool;
    public function sanitizeFilename(string $filename): string;
    public function storeFile(UploadedFile $file, string $path): string;
}
```

## Performance Optimization

### Database Optimization

**Query Optimization**
- **Indexing Strategy**: Proper indexes on frequently queried columns
- **Query Caching**: Cache frequently accessed data
- **Connection Pooling**: Efficient database connection management
- **Lazy Loading**: Load related data only when needed

**Caching Strategy**
```php
namespace App\Services;

class CacheService
{
    public function remember(string $key, int $ttl, callable $callback): mixed;
    public function forget(string $key): bool;
    public function flush(): bool;
}
```

### Frontend Optimization

**Asset Management**
- **CSS/JS Minification**: Compressed assets for production
- **Image Optimization**: WebP format with fallbacks
- **Lazy Loading**: Defer loading of non-critical resources
- **CDN Integration**: Static asset delivery via CDN

**Progressive Web App Features**
- **Service Worker**: Offline functionality for critical features
- **App Manifest**: Installable web app
- **Push Notifications**: Real-time updates for shipment status

## Migration Strategy

### Phase 1: Framework Migration
1. **Environment Setup**: Configure CI 4.x with PHP 8.0
2. **Core Migration**: Migrate controllers, models, and views
3. **Database Migration**: Update schema and create migrations
4. **Authentication**: Implement new authentication system

### Phase 2: UI Modernization
1. **Design System**: Implement new design components
2. **Responsive Layout**: Create mobile-friendly interfaces
3. **User Experience**: Add interactive features and validation
4. **Accessibility**: Ensure WCAG compliance

### Phase 3: Security and Performance
1. **Security Hardening**: Implement security best practices
2. **Performance Optimization**: Add caching and optimization
3. **Testing**: Comprehensive testing suite
4. **Documentation**: Complete system documentation

### Phase 4: User Management and Validation
1. **User Creation**: Set up three-tier user system
2. **Data Migration**: Migrate existing data safely
3. **Training Materials**: Create user guides and documentation
4. **System Validation**: Comprehensive testing and validation of all functionality