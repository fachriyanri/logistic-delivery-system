# User Acceptance Testing Execution Procedures

## Overview

This document provides detailed procedures for executing User Acceptance Testing (UAT) for the CodeIgniter 4 Logistics Application. It includes step-by-step instructions for test preparation, execution, and reporting.

## Pre-Test Preparation

### 1. Environment Setup

#### System Requirements Check
```bash
# Verify PHP version
php --version  # Should be 8.0.6 or higher

# Verify CodeIgniter 4 installation
php spark --version

# Check database connectivity
php spark migrate:status

# Verify file permissions
ls -la writable/
```

#### Database Preparation
1. **Backup Production Data** (if applicable)
   ```bash
   mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Create Clean Test Database**
   ```sql
   CREATE DATABASE logistics_test;
   USE logistics_test;
   ```

3. **Run Migrations**
   ```bash
   php spark migrate
   ```

4. **Load Test Data**
   ```bash
   php spark db:seed UserSeeder
   # Or run the comprehensive test data preparation
   vendor/bin/phpunit tests/UserAcceptance/TestDataPreparation.php::prepareAllTestData
   ```

### 2. User Account Verification

#### Test User Credentials
| Role | Username | Password | Level |
|------|----------|----------|-------|
| Admin | adminpuninar | AdminPuninar123 | 1 |
| Kurir | kurirpuninar | KurirPuninar123 | 2 |
| Gudang | gudangpuninar | GudangPuninar123 | 3 |
| Test User | testuser01 | TestUser123 | 3 |

#### Verify User Access
1. Login with each user account
2. Verify appropriate dashboard access
3. Check role-based menu visibility
4. Confirm permission restrictions

### 3. Test Environment Validation

#### System Health Check
```bash
# Run quick validation
php spark test:validate-environment

# Check all system components
vendor/bin/phpunit tests/SystemValidation/SystemValidationRunner.php::runQuickValidation
```

#### Browser Compatibility Setup
- **Chrome** (latest version)
- **Firefox** (latest version)
- **Safari** (latest version)
- **Edge** (latest version)

#### Mobile Device Setup
- **iOS Device** (iPhone/iPad with Safari)
- **Android Device** (Chrome browser)
- **Responsive Testing Tools** (Browser dev tools)

## Test Execution Process

### Phase 1: Functional Testing by Role

#### Admin User Testing (2-3 hours)

**Setup:**
1. Login as `adminpuninar`
2. Verify admin dashboard loads correctly
3. Check all admin menu items are visible

**Test Scenarios:**
1. **User Management (30 minutes)**
   - Navigate to User Management
   - Create new user account
   - Modify existing user permissions
   - Test password reset functionality
   - Deactivate/reactivate user accounts

2. **System Configuration (30 minutes)**
   - Access system settings
   - Review audit logs
   - Check migration status
   - Test backup procedures

3. **Complete Business Operations (60 minutes)**
   - Create categories and items
   - Add customers and couriers
   - Process complete shipment workflow
   - Generate comprehensive reports

4. **Data Management (30 minutes)**
   - Test data import/export
   - Verify data migration tools
   - Check data integrity reports

**Expected Results Documentation:**
- All operations complete successfully
- No error messages or system failures
- Data saves correctly and displays properly
- Reports generate accurate information

#### Kurir User Testing (2 hours)

**Setup:**
1. Login as `financepuninar`
2. Verify finance dashboard displays financial metrics
3. Check finance-specific menu access

**Test Scenarios:**
1. **Financial Reporting (45 minutes)**
   - Generate daily revenue reports
   - Create monthly summaries
   - Test date range filtering
   - Export financial data to Excel

2. **Customer Management (30 minutes)**
   - View customer information
   - Update customer details
   - Generate customer reports
   - Test customer search functionality

3. **Read-Only Inventory Access (30 minutes)**
   - View inventory listings
   - Attempt to modify items (should be restricted)
   - Generate inventory reports
   - Test inventory search and filtering

4. **Report Analysis (15 minutes)**
   - Generate cost analysis reports
   - Test report customization options
   - Verify export functionality

**Expected Results Documentation:**
- Financial data displays accurately
- Appropriate access restrictions enforced
- Reports contain correct calculations
- Export functions work properly

#### Gudang User Testing (2 hours)

**Setup:**
1. Login as `gudangpuninar`
2. Verify warehouse dashboard shows inventory status
3. Check gudang-specific functionality access

**Test Scenarios:**
1. **Inventory Management (45 minutes)**
   - Add new categories and items
   - Update item information
   - Manage inventory quantities
   - Generate inventory reports

2. **Shipping Operations (60 minutes)**
   - Create new shipment orders
   - Add multiple items to shipments
   - Assign couriers and vehicles
   - Generate delivery notes with QR codes

3. **QR Code and Delivery Management (30 minutes)**
   - Test QR code generation
   - Scan QR codes with mobile device
   - Update shipment status via QR scan
   - Upload delivery confirmation photos

4. **Status Management (15 minutes)**
   - Update shipment statuses
   - Mark deliveries as complete
   - Generate delivery reports

**Expected Results Documentation:**
- Inventory operations work correctly
- Shipment workflow completes end-to-end
- QR codes generate and scan properly
- Status updates save and display correctly

### Phase 2: Integration Testing (1 hour)

#### Cross-Role Workflow Testing
1. **Complete Order Lifecycle**
   - Admin creates customer/courier
   - Gudang creates shipment
   - Kurir generates invoice
   - Gudang completes delivery
   - All roles verify final status

2. **Concurrent User Testing**
   - Multiple users access same data
   - Test data consistency
   - Verify no conflicts occur

#### Data Integrity Validation
1. **Relationship Testing**
   - Verify foreign key constraints
   - Test cascading updates/deletes
   - Check data consistency across tables

2. **Audit Trail Verification**
   - Verify all actions are logged
   - Check user activity tracking
   - Validate security event logging

### Phase 3: Technical Testing (1 hour)

#### Performance Testing
1. **Load Testing**
   - Simulate multiple concurrent users
   - Test large dataset handling
   - Measure response times

2. **Browser Compatibility**
   - Test on all supported browsers
   - Verify JavaScript functionality
   - Check responsive design

#### Security Testing
1. **Authentication Testing**
   - Test invalid login attempts
   - Verify session timeout
   - Check password security

2. **Authorization Testing**
   - Attempt unauthorized access
   - Test role-based restrictions
   - Verify data access controls

#### Mobile Testing
1. **Responsive Design**
   - Test on various screen sizes
   - Verify touch interactions
   - Check mobile navigation

2. **Mobile-Specific Features**
   - Test QR code scanning
   - Verify photo upload from camera
   - Check offline functionality

## Test Result Documentation

### Test Execution Log Template

```
Test Scenario: [Scenario Name]
Tester: [Tester Name]
Date/Time: [Execution Date and Time]
Environment: [Test Environment Details]
Browser: [Browser and Version]

Test Steps:
1. [Step 1 description]
   Result: [Pass/Fail]
   Notes: [Any observations]

2. [Step 2 description]
   Result: [Pass/Fail]
   Notes: [Any observations]

Overall Result: [Pass/Fail]
Issues Found: [List any issues]
Screenshots: [Attach relevant screenshots]
```

### Issue Tracking Template

```
Issue ID: UAT-[Number]
Severity: [Critical/High/Medium/Low]
Priority: [High/Medium/Low]
Category: [Functional/UI/Performance/Security]

Description:
[Detailed description of the issue]

Steps to Reproduce:
1. [Step 1]
2. [Step 2]
3. [Step 3]

Expected Result:
[What should happen]

Actual Result:
[What actually happened]

Environment:
- Browser: [Browser and version]
- User Role: [Admin/Kurir/Gudang]
- Date/Time: [When issue occurred]

Screenshots/Evidence:
[Attach relevant files]

Workaround:
[Any temporary workaround if available]
```

### Daily Test Summary Template

```
UAT Daily Summary - [Date]

Test Progress:
- Scenarios Planned: [Number]
- Scenarios Executed: [Number]
- Scenarios Passed: [Number]
- Scenarios Failed: [Number]
- Completion Percentage: [Percentage]

Issues Summary:
- Critical Issues: [Number]
- High Priority Issues: [Number]
- Medium Priority Issues: [Number]
- Low Priority Issues: [Number]

Key Accomplishments:
- [List major completions]

Blockers/Concerns:
- [List any blocking issues]

Next Day Plan:
- [Planned activities for next day]

Tester: [Name]
```

## Test Data Management

### Test Data Refresh Procedure

#### Daily Refresh (if needed)
```bash
# Backup current test data
mysqldump -u username -p logistics_test > test_backup_$(date +%Y%m%d).sql

# Reset to clean state
php spark migrate:refresh

# Reload test data
vendor/bin/phpunit tests/UserAcceptance/TestDataPreparation.php::prepareAllTestData
```

#### Selective Data Reset
```bash
# Reset only shipment data
php spark db:table pengiriman truncate
php spark db:table detail_pengiriman truncate

# Reload shipment test data
vendor/bin/phpunit tests/UserAcceptance/TestDataPreparation.php::prepareShipments
```

### Test Data Validation

#### Data Integrity Check
```bash
# Run data integrity validation
vendor/bin/phpunit tests/UserAcceptance/TestDataPreparation.php::verifyTestDataIntegrity
```

#### Data Consistency Verification
1. **Record Counts**
   - Verify expected number of records in each table
   - Check for orphaned records
   - Validate foreign key relationships

2. **Data Quality**
   - Check for null values in required fields
   - Verify data format consistency
   - Validate business rule compliance

## Quality Assurance Checklist

### Pre-Test Checklist
- [ ] Test environment is properly configured
- [ ] All test user accounts are created and verified
- [ ] Test data is loaded and validated
- [ ] All required browsers are available and updated
- [ ] Mobile devices are configured for testing
- [ ] Test documentation is prepared and accessible
- [ ] Issue tracking system is ready
- [ ] Backup procedures are in place

### During Test Checklist
- [ ] Follow test scenarios exactly as documented
- [ ] Document all results thoroughly
- [ ] Take screenshots for evidence
- [ ] Log issues immediately when found
- [ ] Verify fixes before marking as resolved
- [ ] Communicate blockers promptly
- [ ] Maintain test data integrity

### Post-Test Checklist
- [ ] All test scenarios are completed
- [ ] All issues are documented and categorized
- [ ] Test results are compiled and analyzed
- [ ] Final test report is prepared
- [ ] Recommendations are documented
- [ ] Test environment is cleaned up (if required)
- [ ] Test artifacts are archived

## Escalation Procedures

### Issue Escalation Matrix

| Severity | Response Time | Escalation Path |
|----------|---------------|-----------------|
| Critical | Immediate | Test Lead → Project Manager → Development Team Lead |
| High | 2 hours | Test Lead → Development Team |
| Medium | 1 business day | Test Lead → Development Team |
| Low | 3 business days | Test Lead → Development Team |

### Communication Protocols

#### Daily Standup
- **Time:** [Scheduled time]
- **Participants:** Test team, development team, project manager
- **Agenda:** Progress update, issues discussion, next steps

#### Issue Communication
- **Critical Issues:** Immediate notification via phone/chat
- **High Issues:** Email notification within 2 hours
- **Medium/Low Issues:** Daily summary email

#### Status Reporting
- **Daily:** Test progress summary
- **Weekly:** Comprehensive status report
- **Ad-hoc:** As requested by stakeholders

## Success Criteria

### Functional Criteria
- All business workflows complete successfully
- All user roles can perform their designated functions
- Data integrity is maintained throughout all operations
- Reports generate accurate and complete information

### Performance Criteria
- Page load times under 3 seconds for normal operations
- Report generation completes within 30 seconds
- System supports at least 10 concurrent users
- No memory leaks or performance degradation

### Security Criteria
- All authentication mechanisms work correctly
- Role-based access controls are properly enforced
- No unauthorized access to restricted functions
- All security measures function as designed

### Usability Criteria
- Users can complete tasks efficiently without confusion
- Interface is intuitive and user-friendly
- Error messages are clear and helpful
- System provides appropriate feedback for all actions

### Compatibility Criteria
- System works correctly on all supported browsers
- Mobile functionality works on iOS and Android devices
- Responsive design adapts properly to different screen sizes
- No browser-specific issues or limitations

## Final UAT Sign-off

### Sign-off Criteria
- [ ] All critical and high-priority test scenarios pass
- [ ] No critical or high-severity issues remain unresolved
- [ ] Performance meets acceptable standards
- [ ] Security requirements are fully satisfied
- [ ] User acceptance criteria are met
- [ ] Documentation is complete and accurate

### Sign-off Process
1. **Test Completion Verification**
   - All planned test scenarios executed
   - All issues documented and addressed
   - Test coverage meets requirements

2. **Stakeholder Review**
   - Business users review and approve functionality
   - Technical team confirms system readiness
   - Project manager validates completion criteria

3. **Formal Sign-off**
   - UAT completion certificate signed
   - Go-live approval granted
   - Production deployment authorized

### UAT Completion Certificate Template

```
USER ACCEPTANCE TESTING COMPLETION CERTIFICATE

Project: CodeIgniter 4 Logistics Application Modernization
UAT Period: [Start Date] to [End Date]
Environment: [Test Environment Details]

Test Summary:
- Total Scenarios: [Number]
- Scenarios Passed: [Number]
- Pass Rate: [Percentage]
- Critical Issues: [Number] (All Resolved)
- High Issues: [Number] (All Resolved)

Certification:
We certify that the User Acceptance Testing has been completed successfully
and the system meets all specified requirements and acceptance criteria.

Business User Sign-off:
Name: _________________ Signature: _________________ Date: _________

Technical Lead Sign-off:
Name: _________________ Signature: _________________ Date: _________

Project Manager Sign-off:
Name: _________________ Signature: _________________ Date: _________

Recommendation: APPROVED FOR PRODUCTION DEPLOYMENT
```