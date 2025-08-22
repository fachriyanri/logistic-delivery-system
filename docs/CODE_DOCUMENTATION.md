# Code Documentation Guide

## Overview

This document provides comprehensive documentation for the CodeIgniter Logistics System codebase. All classes, methods, and functions include detailed PHPDoc comments following PSR-5 standards for consistent and maintainable code documentation.

## Documentation Standards

### PHPDoc Format

All code follows PHPDoc documentation standards with the following structure:

```php
/**
 * Brief description of the class/method
 * 
 * Detailed description explaining the purpose, behavior, and usage.
 * Can span multiple lines and include implementation details.
 * 
 * @package    App\Controllers
 * @author     CodeIgniter Logistics System
 * @version    1.0.0
 * @since      2024-01-01
 * 
 * @param  type  $parameter  Parameter description
 * @return type              Return value description
 * @throws ExceptionType     When this exception is thrown
 * 
 * @example
 * // Usage example
 * $result = $object->method($parameter);
 * 
 * @see RelatedClass::method() Related functionality
 */
```

### Required Documentation Elements

1. **Class Documentation**
   - Purpose and responsibility
   - Package namespace
   - Author information
   - Version and creation date
   - Property descriptions

2. **Method Documentation**
   - Brief description
   - Detailed explanation
   - Parameter types and descriptions
   - Return type and description
   - Exceptions thrown
   - Usage examples
   - Related methods/classes

3. **Property Documentation**
   - Purpose and usage
   - Data type
   - Default values
   - Access modifiers

## Controller Documentation

### AuthController

The authentication controller handles user login, logout, and session management with comprehensive security measures.

```php
/**
 * Authentication Controller
 * 
 * Handles user authentication operations including login, logout, and session management.
 * Provides secure authentication with CSRF protection, input validation, and activity logging.
 * 
 * @package App\Controllers
 */
class AuthController extends BaseController
{
    /**
     * Display login form
     * 
     * Shows the login page for user authentication. If user is already authenticated,
     * redirects to the dashboard. Includes CSRF token and validation services.
     * 
     * @return string The rendered login view or redirect response
     */
    public function index(): string
    
    /**
     * Process login authentication
     * 
     * Validates user credentials and creates authenticated session. Includes comprehensive
     * input validation, CSRF protection, password verification, and activity logging.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface|string JSON response for AJAX or redirect
     * @throws \Exception When database operations fail
     */
    public function authenticate()
}
```

### PengirimanController

Manages shipment operations with full CRUD functionality and status management.

```php
/**
 * Shipment Controller
 * 
 * Handles shipment management operations including creation, updates, status changes,
 * and delivery note generation. Provides both web interface and API endpoints.
 * 
 * @package App\Controllers
 */
class PengirimanController extends BaseController
{
    /**
     * Display shipment list with filtering and pagination
     * 
     * @param array $filters Optional filters for date range, status, customer
     * @return string Rendered shipment list view
     */
    public function index(array $filters = []): string
    
    /**
     * Create new shipment with validation and transaction management
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response with result
     * @throws \Exception When validation fails or database error occurs
     */
    public function store()
}
```

## Service Layer Documentation

### PengirimanService

Business logic service for shipment operations with comprehensive validation and data management.

```php
/**
 * Shipment Service
 * 
 * Business logic service for managing shipments in the logistics system.
 * Handles CRUD operations, validation, status management, and related data operations.
 * 
 * @package App\Services
 */
class PengirimanService
{
    /**
     * Create new shipment with validation and transaction management
     * 
     * Creates a new shipment with validation, transaction management, and detail items.
     * Generates unique shipment ID, validates all data, and ensures data integrity.
     * 
     * @param array $data    Shipment data (tanggal, id_pelanggan, id_kurir, etc.)
     * @param array $details Array of shipment detail items with id_barang and qty
     * 
     * @return array Result array with success status, message, and data
     * @throws \Exception When database operations fail
     * 
     * @example
     * $data = [
     *     'tanggal' => '2024-01-01',
     *     'id_pelanggan' => 'CST0001',
     *     'id_kurir' => 'KRR01'
     * ];
     * $details = [
     *     ['id_barang' => 'BRG0001', 'qty' => 5]
     * ];
     * $result = $service->createShipment($data, $details);
     */
    public function createShipment(array $data, array $details): array
}
```

## Model Documentation

### UserModel

Data access layer for user management with security and validation.

```php
/**
 * User Model
 * 
 * Data access layer for user management in the logistics system.
 * Handles CRUD operations, authentication, validation, and user-related operations.
 * 
 * @package App\Models
 * @property string $table Database table name
 * @property array $validationRules Validation rules for data integrity
 */
class UserModel extends Model
{
    /**
     * Generate next user ID in sequential format
     * 
     * Generates the next sequential user ID in format USRnn.
     * Finds the highest existing ID and increments by 1.
     * 
     * @return string Next available user ID (e.g., "USR04")
     * 
     * @example
     * $nextId = $userModel->generateNextId();
     */
    public function generateNextId(): string
    
    /**
     * Update user password with secure hashing
     * 
     * Updates user password using Argon2ID algorithm for security.
     * Automatically hashes the password before database storage.
     * 
     * @param string $userId      The user ID to update
     * @param string $newPassword The new plain text password
     * 
     * @return bool True if update successful, false otherwise
     */
    public function updatePassword(string $userId, string $newPassword): bool
}
```

## Entity Documentation

### UserEntity

Domain entity representing user data with business logic.

```php
/**
 * User Entity
 * 
 * Domain entity representing a system user with authentication and authorization data.
 * Includes business logic for password management and role-based access control.
 * 
 * @package App\Entities
 */
class UserEntity extends Entity
{
    /**
     * Set user password with secure hashing
     * 
     * Hashes and sets the user password using Argon2ID algorithm.
     * Automatically handles password security requirements.
     * 
     * @param string $password Plain text password
     * @return self Returns entity instance for method chaining
     */
    public function setPassword(string $password): self
    
    /**
     * Verify password against stored hash
     * 
     * Verifies a plain text password against the stored hash.
     * Uses PHP's password_verify() for secure comparison.
     * 
     * @param string $password Plain text password to verify
     * @return bool True if password matches, false otherwise
     */
    public function verifyPassword(string $password): bool
}
```

## Filter Documentation

### AuthFilter

Authentication middleware for protecting routes.

```php
/**
 * Authentication Filter
 * 
 * Middleware filter that ensures users are authenticated before accessing protected routes.
 * Redirects unauthenticated users to login page with return URL preservation.
 * 
 * @package App\Filters
 */
class AuthFilter implements FilterInterface
{
    /**
     * Check authentication before request processing
     * 
     * Validates user session and redirects to login if not authenticated.
     * Preserves the requested URL for post-login redirection.
     * 
     * @param RequestInterface $request   The incoming request
     * @param array|null      $arguments Filter arguments
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface|void Redirect response or void to continue
     */
    public function before(RequestInterface $request, $arguments = null)
}
```

## Validation Documentation

### CustomRules

Custom validation rules for business logic validation.

```php
/**
 * Custom Validation Rules
 * 
 * Custom validation rules specific to the logistics system business logic.
 * Extends CodeIgniter's validation with domain-specific rules.
 * 
 * @package App\Validation
 */
class CustomRules
{
    /**
     * Validate shipment ID format
     * 
     * Validates that shipment ID follows the required format: KRMyyyymmddnnn
     * Where yyyy=year, mm=month, dd=day, nnn=sequence number.
     * 
     * @param string $str The shipment ID to validate
     * @return bool True if valid format, false otherwise
     * 
     * @example
     * // Valid: KRM20240101001
     * // Invalid: ABC20240101001
     */
    public function valid_shipment_id(string $str): bool
}
```

## Helper Documentation

### Security Helper

Security utility functions for the application.

```php
/**
 * Generate secure CSRF token
 * 
 * Creates a cryptographically secure CSRF token for form protection.
 * Uses random_bytes() for entropy and base64 encoding for URL safety.
 * 
 * @param int $length Token length in bytes (default: 32)
 * @return string Base64 encoded CSRF token
 * 
 * @example
 * $token = generate_csrf_token();
 * // Returns: "abc123def456..." (base64 encoded)
 */
function generate_csrf_token(int $length = 32): string

/**
 * Sanitize user input for XSS prevention
 * 
 * Removes or encodes potentially dangerous characters from user input.
 * Uses htmlspecialchars() with proper flags for security.
 * 
 * @param string $input Raw user input
 * @return string Sanitized safe output
 */
function sanitize_input(string $input): string
```

## Configuration Documentation

### Database Configuration

Database connection and query configuration.

```php
/**
 * Database Configuration
 * 
 * Database connection settings for the logistics system.
 * Supports multiple environments with proper security measures.
 * 
 * @var array $default Default database connection settings
 */
public array $default = [
    'DSN'      => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'pengiriman',
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => true,
    'charset'  => 'utf8mb4',
    'DBCollat' => 'utf8mb4_general_ci',
];
```

## Testing Documentation

### Unit Test Examples

```php
/**
 * User Model Test
 * 
 * Unit tests for UserModel functionality including CRUD operations,
 * validation, and security features.
 * 
 * @package Tests\Unit\Models
 */
class UserModelTest extends CIUnitTestCase
{
    /**
     * Test user creation with valid data
     * 
     * Verifies that users can be created with valid data and proper
     * password hashing is applied automatically.
     * 
     * @return void
     */
    public function testCreateUserWithValidData(): void
    
    /**
     * Test password verification functionality
     * 
     * Ensures password verification works correctly with hashed passwords
     * and rejects invalid passwords.
     * 
     * @return void
     */
    public function testPasswordVerification(): void
}
```

## API Documentation Integration

All API endpoints include comprehensive documentation:

```php
/**
 * @api {post} /api/v1/shipments Create Shipment
 * @apiName CreateShipment
 * @apiGroup Shipments
 * @apiVersion 1.0.0
 * 
 * @apiDescription Creates a new shipment with validation and detail items.
 * 
 * @apiParam {String} tanggal Shipment date (YYYY-MM-DD format)
 * @apiParam {String} id_pelanggan Customer ID
 * @apiParam {String} id_kurir Courier ID
 * @apiParam {Array} items Array of shipment items
 * 
 * @apiSuccess {Boolean} success Operation success status
 * @apiSuccess {String} message Success message
 * @apiSuccess {Object} data Created shipment data
 * 
 * @apiError {Boolean} success Always false for errors
 * @apiError {String} message Error description
 * @apiError {Array} errors Validation errors array
 */
```

## Documentation Maintenance

### Guidelines for Updates

1. **Update Documentation When:**
   - Adding new methods or classes
   - Changing method signatures
   - Modifying business logic
   - Adding new parameters or return types

2. **Documentation Review Process:**
   - Code reviews must include documentation review
   - Documentation should be updated in the same commit as code changes
   - Examples should be tested and verified

3. **Documentation Standards:**
   - Use clear, concise language
   - Include practical examples
   - Document edge cases and error conditions
   - Reference related functionality

This comprehensive documentation ensures that the codebase is maintainable, understandable, and follows industry best practices for code documentation.