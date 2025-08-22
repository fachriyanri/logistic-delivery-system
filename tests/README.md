# Testing Documentation

This directory contains the comprehensive test suite for the CodeIgniter 4 Logistics Application modernization project.

## Test Structure

```
tests/
├── _support/                    # Test support files and utilities
│   ├── DatabaseTestCase.php     # Base class for database tests
│   └── Database/
│       └── Seeds/               # Test data seeders
├── unit/                        # Unit tests
│   ├── Entities/               # Entity class tests
│   ├── Models/                 # Model class tests
│   └── Services/               # Service class tests
├── feature/                    # Integration tests
│   ├── Controllers/            # Controller integration tests
│   └── Workflows/              # End-to-end workflow tests
├── TestRunner.php              # Test runner utility class
└── README.md                   # This file
```

## Test Categories

### 1. Unit Tests (`tests/unit/`)

Unit tests focus on testing individual components in isolation:

- **Entity Tests**: Test entity classes for data handling, validation, and business logic
- **Model Tests**: Test model classes for database operations, relationships, and validation
- **Service Tests**: Test service classes for business logic and data processing

### 2. Integration Tests (`tests/feature/Controllers/`)

Integration tests verify that different components work together correctly:

- **Controller Tests**: Test HTTP requests, responses, and controller logic
- **Authentication Tests**: Test login, logout, and session management
- **Role-Based Access Tests**: Test user permissions and access control

### 3. End-to-End Workflow Tests (`tests/feature/Workflows/`)

Workflow tests simulate complete user journeys through the application:

- **User Workflow Tests**: Complete login-to-logout user journeys
- **Shipment Workflow Tests**: Full shipment creation and management processes
- **Report Workflow Tests**: Report generation and export functionality

## Running Tests

### Prerequisites

1. Ensure PHPUnit is installed via Composer:
   ```bash
   composer install
   ```

2. Configure test database in `phpunit.xml`

3. Set up test environment variables

### Running All Tests

```bash
# Using PHPUnit directly
vendor/bin/phpunit

# Using the test runner script
php run-tests.php

# With coverage report
vendor/bin/phpunit --coverage-html build/coverage
```

### Running Specific Test Suites

```bash
# Unit tests only
php run-tests.php unit
vendor/bin/phpunit tests/unit

# Integration tests only
php run-tests.php feature
vendor/bin/phpunit tests/feature/Controllers

# Workflow tests only
php run-tests.php workflows
vendor/bin/phpunit tests/feature/Workflows
```

### Running Individual Test Files

```bash
# Run specific test class
vendor/bin/phpunit tests/unit/Models/UserModelTest.php

# Run specific test method
vendor/bin/phpunit --filter testCanCreateUser tests/unit/Models/UserModelTest.php
```

## Test Environment Setup

### Database Configuration

The test suite uses a separate test database to avoid affecting production data. Configure the test database in `phpunit.xml`:

```xml
<env name="database.tests.hostname" value="localhost"/>
<env name="database.tests.database" value="ci4_test"/>
<env name="database.tests.username" value="root"/>
<env name="database.tests.password" value=""/>
```

### Test Data

Test data is automatically seeded before each test using the seeders in `tests/_support/Database/Seeds/`. The test database is refreshed before each test to ensure clean state.

## Writing Tests

### Unit Test Example

```php
<?php

namespace Tests\Unit\Models;

use App\Models\UserModel;
use Tests\Support\DatabaseTestCase;

class UserModelTest extends DatabaseTestCase
{
    private UserModel $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new UserModel();
    }

    public function testCanCreateUser(): void
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'password123',
            'level' => 2,
        ];

        $result = $this->userModel->insert($userData);
        $this->assertTrue($result);
    }
}
```

### Integration Test Example

```php
<?php

namespace Tests\Feature\Controllers;

use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class AuthControllerTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    public function testLoginWithValidCredentials(): void
    {
        $data = [
            'username' => 'testadmin',
            'password' => 'testpass123',
        ];

        $result = $this->post('/auth/authenticate', $data);
        $result->assertRedirectTo('/dashboard');
    }
}
```

## Test Utilities

### DatabaseTestCase

Base class for tests that require database access. Automatically handles:
- Database migrations
- Test data seeding
- Database cleanup between tests

### Test Runner

The `TestRunner` class provides utilities for:
- Running test suites
- Generating coverage reports
- Checking test environment
- Creating test documentation

## Coverage Reports

Generate HTML coverage reports:

```bash
vendor/bin/phpunit --coverage-html build/coverage
```

View the report by opening `build/coverage/index.html` in a browser.

## Continuous Integration

The test suite is designed to run in CI/CD environments. Example GitHub Actions workflow:

```yaml
name: Tests

on: [push, pull_request]

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
      run: vendor/bin/phpunit --coverage-text
```

## Best Practices

1. **Test Naming**: Use descriptive test method names that explain what is being tested
2. **Test Structure**: Follow Arrange-Act-Assert pattern
3. **Test Data**: Use test seeders for consistent test data
4. **Isolation**: Each test should be independent and not rely on other tests
5. **Coverage**: Aim for high test coverage but focus on critical business logic
6. **Performance**: Keep tests fast by using database transactions and minimal setup

## Troubleshooting

### Common Issues

1. **Database Connection Errors**: Ensure test database is configured and accessible
2. **Permission Errors**: Check file permissions for test directories
3. **Memory Issues**: Increase PHP memory limit for large test suites
4. **Slow Tests**: Use database transactions and optimize test data

### Debug Mode

Run tests with verbose output:

```bash
vendor/bin/phpunit --verbose
vendor/bin/phpunit --debug
```

## Contributing

When adding new features:

1. Write tests for new functionality
2. Ensure all existing tests pass
3. Maintain test coverage above 80%
4. Follow existing test patterns and conventions
5. Update test documentation as needed