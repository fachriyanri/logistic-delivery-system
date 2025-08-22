<?php

namespace Tests\SystemValidation;

/**
 * System Validation Runner
 * 
 * Orchestrates comprehensive system testing including functionality,
 * security, and performance validation across all user roles.
 */
class SystemValidationRunner
{
    private array $testResults = [];
    private string $reportPath;

    public function __construct()
    {
        $this->reportPath = WRITEPATH . 'validation_reports/';
        if (!is_dir($this->reportPath)) {
            mkdir($this->reportPath, 0755, true);
        }
    }

    /**
     * Run all system validation tests
     */
    public function runAllValidationTests(): void
    {
        echo "=== STARTING COMPREHENSIVE SYSTEM VALIDATION ===\n";
        echo "Date: " . date('Y-m-d H:i:s') . "\n";
        echo "Environment: " . ENVIRONMENT . "\n\n";

        // Run comprehensive system tests
        echo "1. Running Comprehensive System Tests...\n";
        $this->runComprehensiveSystemTests();

        // Run security validation tests
        echo "\n2. Running Security Validation Tests...\n";
        $this->runSecurityValidationTests();

        // Run performance validation tests
        echo "\n3. Running Performance Validation Tests...\n";
        $this->runPerformanceValidationTests();

        // Generate final validation report
        echo "\n4. Generating Final Validation Report...\n";
        $this->generateFinalValidationReport();

        echo "\n=== SYSTEM VALIDATION COMPLETED ===\n";
    }

    /**
     * Run comprehensive system tests
     */
    private function runComprehensiveSystemTests(): void
    {
        $command = 'vendor/bin/phpunit tests/SystemValidation/ComprehensiveSystemTest.php --verbose';
        $output = $this->executeCommand($command);
        
        $this->testResults['comprehensive'] = [
            'command' => $command,
            'output' => $output,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Run security validation tests
     */
    private function runSecurityValidationTests(): void
    {
        $command = 'vendor/bin/phpunit tests/SystemValidation/SecurityValidationTest.php --verbose';
        $output = $this->executeCommand($command);
        
        $this->testResults['security'] = [
            'command' => $command,
            'output' => $output,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Run performance validation tests
     */
    private function runPerformanceValidationTests(): void
    {
        $command = 'vendor/bin/phpunit tests/SystemValidation/PerformanceValidationTest.php --verbose';
        $output = $this->executeCommand($command);
        
        $this->testResults['performance'] = [
            'command' => $command,
            'output' => $output,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Execute command and capture output
     */
    private function executeCommand(string $command): array
    {
        $output = [];
        $returnCode = 0;
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        return [
            'output_lines' => $output,
            'return_code' => $returnCode,
            'success' => $returnCode === 0
        ];
    }

    /**
     * Generate final validation report
     */
    private function generateFinalValidationReport(): void
    {
        $reportFile = $this->reportPath . 'system_validation_report_' . date('Y-m-d_H-i-s') . '.md';
        
        $report = $this->buildValidationReport();
        
        file_put_contents($reportFile, $report);
        
        echo "Validation report saved to: {$reportFile}\n";
        
        // Also create a summary report
        $summaryFile = $this->reportPath . 'validation_summary.md';
        $summary = $this->buildValidationSummary();
        file_put_contents($summaryFile, $summary);
        
        echo "Validation summary saved to: {$summaryFile}\n";
    }

    /**
     * Build comprehensive validation report
     */
    private function buildValidationReport(): string
    {
        $report = "# System Validation Report\n\n";
        $report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n";
        $report .= "**Environment:** " . ENVIRONMENT . "\n";
        $report .= "**CodeIgniter Version:** " . \CodeIgniter\CodeIgniter::CI_VERSION . "\n";
        $report .= "**PHP Version:** " . PHP_VERSION . "\n\n";

        $report .= "## Executive Summary\n\n";
        $report .= $this->generateExecutiveSummary() . "\n\n";

        $report .= "## Test Results Overview\n\n";
        
        foreach ($this->testResults as $testType => $result) {
            $report .= "### " . ucfirst($testType) . " Tests\n\n";
            $report .= "**Status:** " . ($result['output']['success'] ? '✅ PASSED' : '❌ FAILED') . "\n";
            $report .= "**Executed:** " . $result['timestamp'] . "\n";
            $report .= "**Command:** `" . $result['command'] . "`\n\n";
            
            if (!empty($result['output']['output_lines'])) {
                $report .= "**Output:**\n```\n";
                $report .= implode("\n", array_slice($result['output']['output_lines'], -20)); // Last 20 lines
                $report .= "\n```\n\n";
            }
        }

        $report .= "## Detailed Analysis\n\n";
        $report .= $this->generateDetailedAnalysis() . "\n\n";

        $report .= "## Recommendations\n\n";
        $report .= $this->generateRecommendations() . "\n\n";

        $report .= "## System Readiness Assessment\n\n";
        $report .= $this->generateReadinessAssessment() . "\n\n";

        return $report;
    }

    /**
     * Build validation summary
     */
    private function buildValidationSummary(): string
    {
        $summary = "# System Validation Summary\n\n";
        $summary .= "**Last Updated:** " . date('Y-m-d H:i:s') . "\n\n";

        $totalTests = count($this->testResults);
        $passedTests = 0;
        
        foreach ($this->testResults as $result) {
            if ($result['output']['success']) {
                $passedTests++;
            }
        }

        $successRate = $totalTests > 0 ? ($passedTests / $totalTests) * 100 : 0;

        $summary .= "## Overall Status\n\n";
        $summary .= "- **Total Test Suites:** {$totalTests}\n";
        $summary .= "- **Passed:** {$passedTests}\n";
        $summary .= "- **Failed:** " . ($totalTests - $passedTests) . "\n";
        $summary .= "- **Success Rate:** " . number_format($successRate, 1) . "%\n\n";

        $summary .= "## Test Suite Status\n\n";
        foreach ($this->testResults as $testType => $result) {
            $status = $result['output']['success'] ? '✅' : '❌';
            $summary .= "- {$status} **" . ucfirst($testType) . " Tests**\n";
        }

        $summary .= "\n## System Readiness\n\n";
        if ($successRate >= 95) {
            $summary .= "🎉 **READY FOR PRODUCTION** - All systems operational\n";
        } elseif ($successRate >= 85) {
            $summary .= "✅ **MOSTLY READY** - Minor issues to address\n";
        } elseif ($successRate >= 70) {
            $summary .= "⚠️ **NEEDS IMPROVEMENT** - Significant issues detected\n";
        } else {
            $summary .= "❌ **NOT READY** - Major issues require resolution\n";
        }

        return $summary;
    }

    /**
     * Generate executive summary
     */
    private function generateExecutiveSummary(): string
    {
        $totalTests = count($this->testResults);
        $passedTests = 0;
        
        foreach ($this->testResults as $result) {
            if ($result['output']['success']) {
                $passedTests++;
            }
        }

        $successRate = $totalTests > 0 ? ($passedTests / $totalTests) * 100 : 0;

        $summary = "This report presents the results of comprehensive system validation testing ";
        $summary .= "for the CodeIgniter 4 Logistics Application modernization project. ";
        $summary .= "The validation covered functionality across all user roles, security measures, ";
        $summary .= "and performance under various load conditions.\n\n";

        $summary .= "**Key Findings:**\n";
        $summary .= "- Overall success rate: " . number_format($successRate, 1) . "%\n";
        $summary .= "- Test suites executed: {$totalTests}\n";
        $summary .= "- Critical systems validated: Authentication, Authorization, Data Integrity, Performance\n";

        if ($successRate >= 95) {
            $summary .= "- **Recommendation:** System is ready for production deployment\n";
        } elseif ($successRate >= 85) {
            $summary .= "- **Recommendation:** System is mostly ready with minor improvements needed\n";
        } else {
            $summary .= "- **Recommendation:** System requires additional work before deployment\n";
        }

        return $summary;
    }

    /**
     * Generate detailed analysis
     */
    private function generateDetailedAnalysis(): string
    {
        $analysis = "### Functionality Testing\n";
        $analysis .= "Comprehensive testing was performed across all three user roles (Admin, Finance, Gudang) ";
        $analysis .= "to ensure proper access controls and functionality availability.\n\n";

        $analysis .= "### Security Testing\n";
        $analysis .= "Security validation covered authentication, authorization, input validation, ";
        $analysis .= "CSRF protection, XSS prevention, SQL injection prevention, and session security.\n\n";

        $analysis .= "### Performance Testing\n";
        $analysis .= "Performance testing evaluated page load times, database query performance, ";
        $analysis .= "memory usage, concurrent user handling, and large dataset processing.\n\n";

        return $analysis;
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(): string
    {
        $recommendations = "";
        $failedTests = [];
        
        foreach ($this->testResults as $testType => $result) {
            if (!$result['output']['success']) {
                $failedTests[] = $testType;
            }
        }

        if (empty($failedTests)) {
            $recommendations .= "✅ **All tests passed successfully!**\n\n";
            $recommendations .= "The system has successfully passed all validation tests and is ready for production deployment. ";
            $recommendations .= "Continue with regular monitoring and maintenance procedures.\n";
        } else {
            $recommendations .= "⚠️ **Issues detected in the following areas:**\n\n";
            
            foreach ($failedTests as $testType) {
                $recommendations .= "- **" . ucfirst($testType) . " Tests:** Review failed test cases and address identified issues\n";
            }
            
            $recommendations .= "\n**Immediate Actions Required:**\n";
            $recommendations .= "1. Review detailed test output for specific failure points\n";
            $recommendations .= "2. Address critical security or functionality issues\n";
            $recommendations .= "3. Re-run validation tests after fixes\n";
            $recommendations .= "4. Consider phased deployment if issues are non-critical\n";
        }

        return $recommendations;
    }

    /**
     * Generate readiness assessment
     */
    private function generateReadinessAssessment(): string
    {
        $totalTests = count($this->testResults);
        $passedTests = 0;
        
        foreach ($this->testResults as $result) {
            if ($result['output']['success']) {
                $passedTests++;
            }
        }

        $successRate = $totalTests > 0 ? ($passedTests / $totalTests) * 100 : 0;

        $assessment = "Based on the comprehensive validation testing, the system readiness is assessed as follows:\n\n";

        if ($successRate >= 95) {
            $assessment .= "🎉 **PRODUCTION READY**\n\n";
            $assessment .= "The system has passed all critical validation tests and is ready for production deployment. ";
            $assessment .= "All functionality, security measures, and performance benchmarks meet requirements.\n\n";
            $assessment .= "**Next Steps:**\n";
            $assessment .= "- Proceed with production deployment\n";
            $assessment .= "- Implement monitoring and alerting\n";
            $assessment .= "- Schedule regular security audits\n";
        } elseif ($successRate >= 85) {
            $assessment .= "✅ **MOSTLY READY**\n\n";
            $assessment .= "The system passes most validation tests with minor issues that should be addressed. ";
            $assessment .= "Consider a phased deployment approach.\n\n";
            $assessment .= "**Next Steps:**\n";
            $assessment .= "- Address identified minor issues\n";
            $assessment .= "- Consider limited production rollout\n";
            $assessment .= "- Monitor closely during initial deployment\n";
        } elseif ($successRate >= 70) {
            $assessment .= "⚠️ **NEEDS IMPROVEMENT**\n\n";
            $assessment .= "The system has significant issues that must be resolved before production deployment. ";
            $assessment .= "Focus on critical functionality and security fixes.\n\n";
            $assessment .= "**Next Steps:**\n";
            $assessment .= "- Address all critical and high-priority issues\n";
            $assessment .= "- Re-run validation tests\n";
            $assessment .= "- Consider additional development time\n";
        } else {
            $assessment .= "❌ **NOT READY**\n\n";
            $assessment .= "The system has major issues that prevent production deployment. ";
            $assessment .= "Significant development work is required.\n\n";
            $assessment .= "**Next Steps:**\n";
            $assessment .= "- Conduct thorough issue analysis\n";
            $assessment .= "- Implement comprehensive fixes\n";
            $assessment .= "- Re-architect problematic components if necessary\n";
            $assessment .= "- Plan for extended development timeline\n";
        }

        return $assessment;
    }

    /**
     * Run quick validation check
     */
    public function runQuickValidation(): void
    {
        echo "Running quick system validation...\n";
        
        // Basic connectivity and configuration checks
        $checks = [
            'Database Connection' => $this->checkDatabaseConnection(),
            'Environment Configuration' => $this->checkEnvironmentConfiguration(),
            'File Permissions' => $this->checkFilePermissions(),
            'Required Extensions' => $this->checkRequiredExtensions(),
            'Security Configuration' => $this->checkSecurityConfiguration()
        ];

        foreach ($checks as $check => $result) {
            $status = $result ? '✅' : '❌';
            echo "  {$status} {$check}\n";
        }
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            $db = \Config\Database::connect();
            $db->query("SELECT 1");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check environment configuration
     */
    private function checkEnvironmentConfiguration(): bool
    {
        return !empty(env('database.default.hostname')) && 
               !empty(env('database.default.database'));
    }

    /**
     * Check file permissions
     */
    private function checkFilePermissions(): bool
    {
        return is_writable(WRITEPATH) && 
               is_writable(WRITEPATH . 'cache') && 
               is_writable(WRITEPATH . 'logs');
    }

    /**
     * Check required PHP extensions
     */
    private function checkRequiredExtensions(): bool
    {
        $required = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl'];
        
        foreach ($required as $extension) {
            if (!extension_loaded($extension)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check security configuration
     */
    private function checkSecurityConfiguration(): bool
    {
        return !empty(env('encryption.key')) && 
               env('app.CSRFProtection') === 'true';
    }
}