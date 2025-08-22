# Developer Guide

## Overview

This comprehensive developer guide provides all necessary information for developers to set up, understand, contribute to, and maintain the CodeIgniter Logistics System. The system has been modernized from CodeIgniter 2.2.0 to CodeIgniter 4.x with PHP 8.0.6 compatibility.

## Table of Contents

1. [Development Environment Setup](#development-environment-setup)
2. [System Architecture](#system-architecture)
3. [Code Structure and Organization](#code-structure-and-organization)
4. [Development Workflow](#development-workflow)
5. [Database Management](#database-management)
6. [Testing Guidelines](#testing-guidelines)
7. [Security Implementation](#security-implementation)
8. [Performance Optimization](#performance-optimization)
9. [Deployment Procedures](#deployment-procedures)
10. [Troubleshooting and Debugging](#troubleshooting-and-debugging)

## Development Environment Setup

### System Requirements

#### Minimum Requirements
- **PHP**: 8.0.6 or higher
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Memory**: 512MB RAM minimum, 2GB recommended
- **Storage**: 1GB free disk space

#### Recommended Development Tools
- **IDE**: Visual Studio Code, PhpStorm, or Sublime Text
- **Version Control**: Git 2.30+
- **Package Manager**: Composer 2.0+
- **Database Tool**: phpMyAdmin, MySQL Workbench, or DBeaver
- **API Testing**: Postman or Insomnia

### Installation Steps

#### 1. Clone Repository
```bash
git clone https://github.com/your-org/codeigniter-logistics.git
cd codeigniter-logistics
```

#### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install development dependencies
composer install --dev
```

#### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Edit environment variables
nano .env
```

#### 4. Environment Variables
```env
# Application Settings
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080'
app.appTimezone = 'Asia/Jakarta'

# Database Configuration
database.default.hostname = localhost
database.default.database = pengiriman
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi

# Security Settings
encryption.key = your-32-character-secret-key
security.csrfProtection = 'cookie'
security.tokenRandomize = true

# Session Configuration
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
session.cookieName = 'ci_session'
session.expiration = 7200
```

#### 5. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE pengiriman CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

# Run migrations
php spark migrate

# Run seeders
php spark db:seed UserSeeder
php spark db:seed KategoriSeeder
```

#### 6. File Permissions
```bash
# Set proper permissions
chmod -R 755 writable/
chmod -R 755 public/
chown -R www-data:www-data writable/
```

#### 7. Start Development Server
```bash
# Using CodeIgniter's built-in server
php spark serve

# Or using specific host and port
php spark serve --host 0.0.0.0 --port 8080
```

### Development Tools Configuration

#### Visual Studio Code Extensions
```json
{
    "recommendations": [
        "bmewburn.vscode-intelephense-client",
        "ms-vscode.vscode-json",
        "bradlc.vscode-tailwindcss",
        "formulahendry.auto-rename-tag",
        "christian-kohler.path-intellisense"
    ]
}
```

#### PhpStorm Configuration
- Enable CodeIgniter 4 support
- Configure PHP interpreter to use PHP 8.0+
- Set up database connection
- Configure code style to PSR-12

## System Architecture

### Framework Architecture

#### MVC Pattern Enhancement
The system follows an enhanced MVC pattern with additional layers:

```
┌─────────────────┐
│   Presentation  │ ← Views, Controllers, Filters
├─────────────────┤
│   Application   │ ← Services, Libraries, Helpers
├─────────────────┤
│     Domain      │ ← Entities, Models, Validation
├─────────────────┤
│ Infrastructure  │ ← Database, Cache, File System
└─────────────────┘
```

#### Directory Structure
```
app/
├── Commands/           # CLI commands
├── Config/            # Configuration files
├── Controllers/       # HTTP request handlers
├── Database/          # Migrations and seeds
├── Entities/          # Domain entities
├── Filters/           # Request/response filters
├── Helpers/           # Utility functions
├── Libraries/         # Custom libraries
├── Models/            # Data access layer
├── Services/          # Business logic layer
├── Validation/        # Custom validation rules
└── Views/             # Presentation templates
```

### Design Patterns Implementation

#### Repository Pattern
```php
interface PengirimanRepositoryInterface
{
    public function find(string $id): ?PengirimanEntity;
    public function findAll(array $criteria = []): array;
    public function save(PengirimanEntity $entity): bool;
    public function delete(string $id): bool;
}

class PengirimanModel extends Model implements PengirimanRepositoryInterface
{
    // Implementation
}
```

#### Service Layer Pattern
```php
class PengirimanService
{
    private PengirimanRepositoryInterface $repository;
    private ValidationService $validator;
    
    public function createShipment(array $data): Result
    {
        // Business logic implementation
    }
}
```

#### Entity Pattern
```php
class PengirimanEntity extends Entity
{
    protected $attributes = [
        'id_pengiriman' => null,
        'tanggal' => null,
        // ... other attributes
    ];
    
    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }
}
```

## Code Structure and Organization

### Naming Conventions

#### Classes
- **Controllers**: PascalCase with "Controller" suffix
  ```php
  class PengirimanController extends BaseController
  ```

- **Models**: PascalCase with "Model" suffix
  ```php
  class PengirimanModel extends Model
  ```

- **Entities**: PascalCase with "Entity" suffix
  ```php
  class PengirimanEntity extends Entity
  ```

- **Services**: PascalCase with "Service" suffix
  ```php
  class PengirimanService
  ```

#### Methods and Variables
- **Methods**: camelCase
  ```php
  public function createShipment(array $data): array
  ```

- **Variables**: camelCase
  ```php
  protected $pengirimanModel;
  ```

- **Constants**: UPPER_SNAKE_CASE
  ```php
  const STATUS_DELIVERED = 1;
  ```

#### Database
- **Tables**: snake_case
  ```sql
  CREATE TABLE pengiriman (...)
  ```

- **Columns**: snake_case
  ```sql
  id_pengiriman VARCHAR(14)
  ```

### Code Organization Principles

#### Single Responsibility Principle
Each class should have only one reason to change:

```php
// Good: Single responsibility
class PengirimanValidator
{
    public function validate(array $data): array
    {
        // Only validation logic
    }
}

class PengirimanService
{
    public function createShipment(array $data): array
    {
        // Only business logic
    }
}
```

#### Dependency Injection
Use dependency injection for better testability:

```php
class PengirimanController extends BaseController
{
    private PengirimanService $pengirimanService;
    
    public function __construct(PengirimanService $pengirimanService)
    {
        $this->pengirimanService = $pengirimanService;
    }
}
```

#### Interface Segregation
Create focused interfaces:

```php
interface Trackable
{
    public function getTrackingId(): string;
    public function getStatus(): int;
}

interface Billable
{
    public function getBillingAmount(): float;
    public function getBillingDate(): DateTime;
}
```

## Development Workflow

### Git Workflow

#### Branch Strategy
```
main
├── develop
│   ├── feature/user-management
│   ├── feature/shipment-tracking
│   └── feature/reporting-system
├── release/v1.0.0
└── hotfix/critical-bug-fix
```

#### Commit Message Format
```
type(scope): description

[optional body]

[optional footer]
```

Examples:
```
feat(shipment): add QR code generation
fix(auth): resolve session timeout issue
docs(api): update endpoint documentation
test(model): add unit tests for UserModel
```

#### Pull Request Process
1. Create feature branch from develop
2. Implement feature with tests
3. Update documentation
4. Create pull request
5. Code review and approval
6. Merge to develop branch

### Code Review Guidelines

#### Review Checklist
- [ ] Code follows PSR-12 standards
- [ ] All methods have PHPDoc comments
- [ ] Unit tests are included
- [ ] Security best practices followed
- [ ] Performance considerations addressed
- [ ] Error handling implemented
- [ ] Documentation updated

#### Review Process
1. **Automated Checks**: CI/CD pipeline runs tests
2. **Peer Review**: At least one developer reviews
3. **Security Review**: Security-sensitive changes reviewed by senior developer
4. **Documentation Review**: Ensure documentation is updated

### Continuous Integration

#### GitHub Actions Workflow
```yaml
name: CI/CD Pipeline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.0'
        
    - name: Install dependencies
      run: composer install
      
    - name: Run tests
      run: vendor/bin/phpunit
      
    - name: Code coverage
      run: vendor/bin/phpunit --coverage-clover coverage.xml
```

## Database Management

### Migration System

#### Creating Migrations
```bash
# Create new migration
php spark make:migration CreateShipmentTable

# Run migrations
php spark migrate

# Rollback migrations
php spark migrate:rollback
```

#### Migration Example
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengirimanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengiriman' => [
                'type' => 'VARCHAR',
                'constraint' => 14,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id_pengiriman');
        $this->forge->addKey('tanggal');
        $this->forge->createTable('pengiriman');
    }

    public function down()
    {
        $this->forge->dropTable('pengiriman');
    }
}
```

### Database Seeding

#### Creating Seeders
```bash
# Create seeder
php spark make:seeder UserSeeder

# Run specific seeder
php spark db:seed UserSeeder

# Run all seeders
php spark db:seed
```

#### Seeder Example
```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user' => 'USR01',
                'username' => 'adminpuninar',
                'password' => password_hash('AdminPuninar123', PASSWORD_ARGON2ID),
                'level' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            // ... more users
        ];

        $this->db->table('user')->insertBatch($data);
    }
}
```

### Query Optimization

#### Indexing Strategy
```sql
-- Primary keys (automatic)
ALTER TABLE pengiriman ADD PRIMARY KEY (id_pengiriman);

-- Foreign keys
ALTER TABLE pengiriman ADD INDEX idx_pelanggan (id_pelanggan);
ALTER TABLE pengiriman ADD INDEX idx_kurir (id_kurir);

-- Search fields
ALTER TABLE pengiriman ADD INDEX idx_tanggal (tanggal);
ALTER TABLE pengiriman ADD INDEX idx_status (status);

-- Composite indexes
ALTER TABLE detail_pengiriman ADD INDEX idx_shipment_item (id_pengiriman, id_barang);
```

#### Query Performance
```php
// Good: Use specific fields
$shipments = $this->pengirimanModel
    ->select('id_pengiriman, tanggal, status')
    ->where('tanggal >=', $dateFrom)
    ->where('tanggal <=', $dateTo)
    ->findAll();

// Good: Use joins instead of multiple queries
$shipments = $this->pengirimanModel
    ->select('p.*, pel.nama as customer_name, k.nama as courier_name')
    ->join('pelanggan pel', 'pel.id_pelanggan = p.id_pelanggan')
    ->join('kurir k', 'k.id_kurir = p.id_kurir')
    ->findAll();
```

## Testing Guidelines

### Testing Structure

#### Test Directory Structure
```
tests/
├── _support/
│   ├── Database/
│   │   └── Migrations/
│   └── Models/
├── feature/
│   ├── Controllers/
│   └── Workflows/
└── unit/
    ├── Entities/
    ├── Models/
    └── Services/
```

### Unit Testing

#### Model Testing Example
```php
<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\UserModel;

class UserModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $seed = 'UserSeeder';

    public function testGenerateNextId()
    {
        $userModel = new UserModel();
        $nextId = $userModel->generateNextId();
        
        $this->assertStringStartsWith('USR', $nextId);
        $this->assertEquals(5, strlen($nextId));
    }

    public function testCreateUserWithValidData()
    {
        $userModel = new UserModel();
        $userData = [
            'id_user' => 'USR99',
            'username' => 'testuser',
            'password' => 'password123',
            'level' => 1
        ];

        $result = $userModel->insert($userData);
        $this->assertTrue($result);

        $user = $userModel->find('USR99');
        $this->assertEquals('testuser', $user->username);
    }
}
```

#### Service Testing Example
```php
<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PengirimanService;
use App\Models\PengirimanModel;

class PengirimanServiceTest extends CIUnitTestCase
{
    private $pengirimanService;
    private $pengirimanModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pengirimanModel = $this->createMock(PengirimanModel::class);
        $this->pengirimanService = new PengirimanService();
    }

    public function testCreateShipmentWithValidData()
    {
        $shipmentData = [
            'tanggal' => '2024-01-01',
            'id_pelanggan' => 'CST0001',
            'id_kurir' => 'KRR01',
            'no_po' => 'PO123456',
            'no_kendaraan' => 'B1234ABC'
        ];

        $details = [
            ['id_barang' => 'BRG0001', 'qty' => 5]
        ];

        $result = $this->pengirimanService->createShipment($shipmentData, $details);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }
}
```

### Integration Testing

#### Controller Testing Example
```php
<?php

namespace Tests\Feature\Controllers;

use CodeIgniter\Test\FeatureTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class AuthControllerTest extends FeatureTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $seed = 'UserSeeder';

    public function testLoginWithValidCredentials()
    {
        $result = $this->post('/auth/authenticate', [
            'username' => 'adminpuninar',
            'password' => 'AdminPuninar123'
        ]);

        $result->assertRedirectTo('/dashboard');
        $this->assertTrue($this->session()->has('isLoggedIn'));
    }

    public function testLoginWithInvalidCredentials()
    {
        $result = $this->post('/auth/authenticate', [
            'username' => 'invalid',
            'password' => 'invalid'
        ]);

        $result->assertRedirectTo('/login');
        $this->assertFalse($this->session()->has('isLoggedIn'));
    }
}
```

### Running Tests

#### Command Line Testing
```bash
# Run all tests
vendor/bin/phpunit

# Run specific test suite
vendor/bin/phpunit tests/unit/

# Run specific test file
vendor/bin/phpunit tests/unit/Models/UserModelTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/

# Run with specific configuration
vendor/bin/phpunit --configuration phpunit.xml
```

#### Test Configuration
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/codeigniter4/framework/system/Test/bootstrap.php"
         backupGlobals="false"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         stopOnError="false"
         stopOnFailure="false"
         stopOnIncomplete="false"
         stopOnSkipped="false">
    
    <testsuites>
        <testsuite name="Unit">
            <directory>./tests/unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>./tests/feature</directory>
        </testsuite>
    </testsuites>
    
    <filter>
        <whitelist addUncoveredFilesFromWhitelist="true">
            <directory suffix=".php">./app</directory>
            <exclude>
                <directory>./app/Views</directory>
            </exclude>
        </whitelist>
    </filter>
</phpunit>
```

## Security Implementation

### Authentication Security

#### Password Hashing
```php
// Always use Argon2ID for password hashing
$hashedPassword = password_hash($plainPassword, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 3
]);

// Verify passwords
if (password_verify($plainPassword, $hashedPassword)) {
    // Password is correct
}
```

#### Session Security
```php
// app/Config/App.php
public $sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler';
public $sessionCookieName = 'ci_session';
public $sessionExpiration = 7200;
public $sessionSavePath = WRITEPATH . 'session';
public $sessionMatchIP = false;
public $sessionTimeToUpdate = 300;
public $sessionRegenerateDestroy = false;

// Secure session configuration
public $cookieSecure = true;    // HTTPS only
public $cookieHTTPOnly = true;  // No JavaScript access
public $cookieSameSite = 'Strict';
```

### Input Validation and Sanitization

#### Validation Rules
```php
// app/Config/Validation.php
public $shipment = [
    'tanggal' => 'required|valid_date[Y-m-d]',
    'id_pelanggan' => 'required|is_not_unique[pelanggan.id_pelanggan]',
    'id_kurir' => 'required|is_not_unique[kurir.id_kurir]',
    'no_po' => 'required|max_length[15]|alpha_numeric_punct',
    'no_kendaraan' => 'required|max_length[8]|alpha_numeric_space'
];
```

#### Custom Validation Rules
```php
// app/Validation/CustomRules.php
class CustomRules
{
    public function valid_shipment_id(string $str): bool
    {
        return preg_match('/^KRM\d{8}\d{3}$/', $str) === 1;
    }

    public function valid_phone_number(string $str): bool
    {
        return preg_match('/^(\+62|62|0)[0-9]{9,13}$/', $str) === 1;
    }
}
```

### CSRF Protection

#### Configuration
```php
// app/Config/Security.php
public $csrfProtection = 'cookie';
public $tokenRandomize = true;
public $tokenName = 'csrf_token';
public $headerName = 'X-CSRF-TOKEN';
public $cookieName = 'csrf_cookie';
public $expires = 7200;
public $regenerate = true;
```

#### Implementation in Views
```php
<!-- In forms -->
<?= csrf_field() ?>

<!-- In AJAX requests -->
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>
```

### XSS Prevention

#### Output Escaping
```php
// Always escape output in views
<?= esc($user->username) ?>
<?= esc($shipment->notes, 'html') ?>

// For HTML content (use with caution)
<?= esc($content, 'raw') ?>
```

#### Content Security Policy
```php
// app/Config/ContentSecurityPolicy.php
public $defaultSrc = "'self'";
public $scriptSrc = "'self' 'unsafe-inline'";
public $styleSrc = "'self' 'unsafe-inline'";
public $imgSrc = "'self' data: https:";
public $fontSrc = "'self'";
public $connectSrc = "'self'";
```

## Performance Optimization

### Database Optimization

#### Query Optimization
```php
// Use specific fields instead of SELECT *
$shipments = $this->pengirimanModel
    ->select('id_pengiriman, tanggal, status')
    ->findAll();

// Use pagination for large datasets
$shipments = $this->pengirimanModel
    ->paginate(20);

// Use database-level filtering
$shipments = $this->pengirimanModel
    ->where('tanggal >=', $dateFrom)
    ->where('status', 1)
    ->findAll();
```

#### Caching Implementation
```php
// app/Services/CacheService.php
class CacheService
{
    private $cache;

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    public function remember(string $key, int $ttl, callable $callback)
    {
        $data = $this->cache->get($key);
        
        if ($data === null) {
            $data = $callback();
            $this->cache->save($key, $data, $ttl);
        }
        
        return $data;
    }
}

// Usage in services
public function getCustomersForSelect(): array
{
    return $this->cacheService->remember('customers_select', 3600, function() {
        return $this->pelangganModel->getCustomersForSelect();
    });
}
```

### Frontend Optimization

#### Asset Minification
```php
// app/Helpers/asset_helper.php
function minify_css(string $css): string
{
    $css = preg_replace('/\s+/', ' ', $css);
    $css = preg_replace('/\/\*.*?\*\//', '', $css);
    return trim($css);
}

function minify_js(string $js): string
{
    // Basic minification - use proper tools for production
    $js = preg_replace('/\s+/', ' ', $js);
    $js = preg_replace('/\/\*.*?\*\//', '', $js);
    return trim($js);
}
```

#### Lazy Loading
```javascript
// Lazy load images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
});
```

## Deployment Procedures

### Production Deployment

#### Pre-deployment Checklist
- [ ] All tests pass
- [ ] Code review completed
- [ ] Security scan completed
- [ ] Performance testing completed
- [ ] Database migrations tested
- [ ] Backup procedures verified
- [ ] Rollback plan prepared

#### Deployment Steps
```bash
# 1. Backup current system
mysqldump -u root -p pengiriman > backup_$(date +%Y%m%d_%H%M%S).sql
tar -czf app_backup_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/html/

# 2. Update code
git pull origin main
composer install --no-dev --optimize-autoloader

# 3. Run migrations
php spark migrate --env=production

# 4. Clear cache
php spark cache:clear

# 5. Update file permissions
chmod -R 755 writable/
chown -R www-data:www-data writable/

# 6. Restart services
sudo systemctl restart apache2
sudo systemctl restart mysql
```

#### Environment Configuration
```env
# Production environment
CI_ENVIRONMENT = production
app.baseURL = 'https://logistics.yourcompany.com'

# Security settings
security.csrfProtection = 'cookie'
security.tokenRandomize = true

# Database settings
database.default.hostname = prod-db-server
database.default.database = pengiriman_prod
database.default.username = prod_user
database.default.password = secure_password

# Logging
logger.threshold = 4
```

### Monitoring and Maintenance

#### Health Checks
```php
// app/Controllers/HealthController.php
class HealthController extends BaseController
{
    public function check()
    {
        $health = [
            'status' => 'ok',
            'timestamp' => date('c'),
            'checks' => [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
                'storage' => $this->checkStorage(),
            ]
        ];

        return $this->response->setJSON($health);
    }

    private function checkDatabase(): array
    {
        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');
            return ['status' => 'ok'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
```

#### Log Monitoring
```php
// Custom log monitoring
log_message('info', 'Shipment created: ' . $shipmentId);
log_message('error', 'Database connection failed: ' . $error);
log_message('critical', 'Security breach detected: ' . $details);
```

## Troubleshooting and Debugging

### Common Issues

#### Database Connection Issues
```php
// Debug database connection
try {
    $db = \Config\Database::connect();
    $query = $db->query('SELECT 1');
    echo "Database connection successful";
} catch (\Exception $e) {
    echo "Database error: " . $e->getMessage();
}
```

#### Session Issues
```php
// Debug session problems
var_dump($_SESSION);
var_dump(session_status());
var_dump(session_id());

// Check session configuration
$config = config('App');
var_dump($config->sessionDriver);
var_dump($config->sessionSavePath);
```

#### Permission Issues
```bash
# Check file permissions
ls -la writable/
ls -la public/

# Fix permissions
sudo chown -R www-data:www-data writable/
sudo chmod -R 755 writable/
```

### Debugging Tools

#### CodeIgniter Debug Toolbar
```php
// app/Config/Toolbar.php
public $collectors = [
    \CodeIgniter\Debug\Toolbar\Collectors\Timers::class,
    \CodeIgniter\Debug\Toolbar\Collectors\Database::class,
    \CodeIgniter\Debug\Toolbar\Collectors\Logs::class,
    \CodeIgniter\Debug\Toolbar\Collectors\Views::class,
];
```

#### Custom Debugging
```php
// Debug helper functions
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function debug_log($message, $data = null) {
    $log = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($data) {
        $log .= ' - ' . json_encode($data);
    }
    file_put_contents(WRITEPATH . 'logs/debug.log', $log . PHP_EOL, FILE_APPEND);
}
```

### Performance Debugging

#### Query Analysis
```php
// Enable query debugging
$db = \Config\Database::connect();
$db->enableQueryLog();

// Your queries here

// Get query log
$queries = $db->getQueryLog();
foreach ($queries as $query) {
    echo $query['sql'] . ' - ' . $query['time'] . 's' . PHP_EOL;
}
```

#### Memory Usage Monitoring
```php
function memory_usage() {
    return [
        'current' => memory_get_usage(true),
        'peak' => memory_get_peak_usage(true),
        'limit' => ini_get('memory_limit')
    ];
}

// Log memory usage
log_message('debug', 'Memory usage: ' . json_encode(memory_usage()));
```

This comprehensive developer guide provides all the necessary information for developers to effectively work with the CodeIgniter Logistics System, from initial setup through production deployment and maintenance.