# Test Directory Structure

This directory contains all testing utilities, integration tests, and test data management tools for the Startup-Investor Platform.

## Directory Structure

```
tests/
├── integration/           # Integration tests for complete workflows
│   ├── MatchingSystemTest.php
│   ├── SearchSystemTest.php
│   └── UserManagementTest.php
├── unit/                  # Unit tests for individual components
│   ├── ModelTests/        # Tests for data models
│   ├── ControllerTests/   # Tests for controllers
│   └── ServiceTests/      # Tests for business logic services
├── utilities/             # Development and testing utilities
│   ├── database-diagnostic.php
│   ├── debug.php
│   ├── setup-directories.php
│   ├── test-data-seeder.php
│   └── cleanup-helper.php
├── fixtures/              # Test data and configurations
│   ├── sample-data/       # Sample CSV, JSON files for testing
│   └── test-configs/      # Test-specific configuration files
└── README.md             # This file
```

## Test Utilities

### Core Utilities

#### `utilities/debug.php`
Complete system diagnostic script that tests:
- Application initialization
- Database connectivity
- Table existence and structure
- Model instantiation
- Controller loading
- File permissions
- Critical file existence

**Usage:** Navigate to `tests/utilities/debug.php` in your browser

#### `utilities/database-diagnostic.php`
Specialized database testing script that performs:
- Connection verification
- Table structure analysis
- Record counting
- Configuration validation
- Table creation permissions testing

**Usage:** Navigate to `tests/utilities/database-diagnostic.php` in your browser

#### `utilities/setup-directories.php`
Directory structure initialization script that:
- Creates all required directories
- Sets proper permissions
- Creates placeholder files
- Generates basic dashboard templates

**Usage:** Navigate to `tests/utilities/setup-directories.php` in your browser

### Test Data Management

#### `utilities/test-data-seeder.php`
Creates realistic test data including:
- 5 test industries (Technology, Healthcare, FinTech, E-commerce, Education)
- 4 test users (2 startup founders, 2 investors)
- 2 test startups with complete profiles
- 2 test investors with investment criteria

**Test Login Credentials:**
- **Startup Founders:**
  - `founder1@testcompany.com` / `testpass123`
  - `founder2@techstartup.com` / `testpass123`
- **Investors:**
  - `investor1@vcfund.com` / `testpass123`
  - `investor2@angelgroup.com` / `testpass123`

**Usage:** Navigate to `tests/utilities/test-data-seeder.php` in your browser

#### `utilities/cleanup-helper.php`
Safely removes test data with options for:
- **Test Data Only:** Removes only seeded test data
- **All Data:** Complete database reset (use with caution)

**Usage:** Navigate to `tests/utilities/cleanup-helper.php` in your browser

## Integration Tests

Integration tests validate complete user workflows from start to finish. Each test includes:
- Setup of test data
- Complete workflow simulation
- Verification of results
- Cleanup of test data

### Available Integration Tests

- **MatchingSystemTest.php:** Tests the complete matching algorithm workflow
- **SearchSystemTest.php:** Tests search functionality for both startups and investors
- **UserManagementTest.php:** Tests user registration, login, and profile management

### Running Integration Tests

Integration tests can be run via command line:

```bash
# From project root
php tests/integration/MatchingSystemTest.php
```

Or accessed via browser for visual output.

## Safety Guidelines

### Why Tests Are Isolated

All test files are contained within the `tests/` directory for several important reasons:

1. **Security:** Test files are not accessible via web browser in production
2. **Organization:** Clear separation between application code and testing tools
3. **Deployment:** Easy to exclude test directory from production deployments
4. **Safety:** Prevents accidental execution of diagnostic or cleanup scripts

### Path References

All test utilities use relative paths from their location:

```php
// From tests/utilities/
require_once __DIR__ . '/../../src/Core/Application.php';
$configFile = __DIR__ . '/../../config/database.php';
```

This ensures tests work regardless of the web server configuration.

## Best Practices

### Before Making Changes

1. Run `utilities/debug.php` to verify system status
2. Create test data with `utilities/test-data-seeder.php`
3. Test your changes with the seeded data
4. Run integration tests to verify workflows

### After Making Changes

1. Run integration tests to ensure nothing broke
2. Update test data if new fields were added
3. Clean up with `utilities/cleanup-helper.php` if needed

### Creating New Tests

When adding new major components, always:

1. Create an integration test in `tests/integration/`
2. Follow the pattern of existing tests (setup, test, cleanup)
3. Include the test in the Critical Path Rule workflow
4. Update this README with new test information

## Critical Path Rule Compliance

This test structure supports the Critical Path Rule by:

- **Integration Tests:** Validate complete component functionality before moving to next component
- **Test Data:** Provides realistic data for testing user workflows
- **Utilities:** Enable safe testing and debugging during development
- **Isolation:** Keeps all test-related code separate from production code

## Troubleshooting

### Common Issues

**Database Connection Errors:**
1. Run `utilities/database-diagnostic.php`
2. Check `config/database.php` settings
3. Verify MySQL service is running

**Missing Tables:**
1. Run `php scripts/migrate.php` from project root
2. Check migration files in `database/migrations/`

**Permission Errors:**
1. Run `utilities/setup-directories.php`
2. Check web server user permissions
3. Verify directory ownership

**Test Data Issues:**
1. Run `utilities/cleanup-helper.php` to reset
2. Re-run `utilities/test-data-seeder.php`
3. Check for duplicate email addresses

### Getting Help

If you encounter issues:

1. Check the debug output from `utilities/debug.php`
2. Review error logs in `storage/logs/`
3. Verify all required directories exist with proper permissions
4. Ensure database migrations have been run

## Security Note

**Never run test utilities in production!** These files should only exist in development environments. The cleanup utility especially can delete all application data.