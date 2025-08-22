<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\DataValidationService;

class ValidateData extends BaseCommand
{
    protected $group       = 'Migration';
    protected $name        = 'validate:data';
    protected $description = 'Validate and clean up migrated data';
    protected $usage       = 'validate:data [options]';
    protected $arguments   = [];
    protected $options     = [
        '--cleanup'   => 'Perform data cleanup after validation',
        '--report'    => 'Generate detailed data quality report',
        '--fix'       => 'Automatically fix common data issues',
    ];

    public function run(array $params)
    {
        $cleanup = CLI::getOption('cleanup') !== null;
        $report = CLI::getOption('report') !== null;
        $fix = CLI::getOption('fix') !== null;

        CLI::write('Starting data validation...', 'green');
        CLI::newLine();

        $validationService = new DataValidationService();

        try {
            // Run validation
            CLI::write('Validating migrated data...', 'yellow');
            $validation = $validationService->validateAllData();
            
            $this->displayValidationResults($validation);

            if ($fix || $cleanup) {
                CLI::newLine();
                CLI::write('Performing data cleanup...', 'yellow');
                $cleanupResults = $validationService->cleanupInvalidData();
                
                $this->displayCleanupResults($cleanupResults);

                // Re-validate after cleanup
                CLI::newLine();
                CLI::write('Re-validating after cleanup...', 'yellow');
                $postCleanupValidation = $validationService->validateAllData();
                
                CLI::write('Post-cleanup validation results:', 'cyan');
                $this->displayValidationSummary($postCleanupValidation);
            }

            if ($report) {
                CLI::newLine();
                CLI::write('Generating data quality report...', 'yellow');
                $qualityReport = $validationService->generateDataQualityReport();
                
                $this->saveDataQualityReport($qualityReport);
            }

            CLI::newLine();
            if ($validation['overall_valid']) {
                CLI::write('✓ Data validation completed successfully!', 'green');
            } else {
                CLI::write('⚠ Data validation found issues that need attention', 'yellow');
            }

        } catch (\Exception $e) {
            CLI::error('Validation failed: ' . $e->getMessage());
        }
    }

    private function displayValidationResults(array $validation): void
    {
        CLI::write('Validation Results:', 'cyan');
        CLI::newLine();

        foreach ($validation as $table => $result) {
            if ($table === 'validation_log' || $table === 'overall_valid') {
                continue;
            }

            $status = $result['valid'] ? '✓' : '✗';
            $color = $result['valid'] ? 'green' : 'red';
            
            CLI::write("  {$status} " . ucfirst($table), $color);
            
            if (isset($result['total_records'])) {
                CLI::write("    Total records: {$result['total_records']}");
            }
            
            if (!empty($result['issues'])) {
                foreach ($result['issues'] as $issue) {
                    CLI::write("    - {$issue}", 'yellow');
                }
            }
            CLI::newLine();
        }

        // Display validation log
        if (!empty($validation['validation_log'])) {
            CLI::write('Validation Log:', 'cyan');
            foreach ($validation['validation_log'] as $entry) {
                CLI::write("  {$entry}");
            }
            CLI::newLine();
        }
    }

    private function displayValidationSummary(array $validation): void
    {
        $totalIssues = 0;
        foreach ($validation as $table => $result) {
            if (isset($result['issues'])) {
                $totalIssues += count($result['issues']);
            }
        }

        if ($validation['overall_valid']) {
            CLI::write('  ✓ All validations passed', 'green');
        } else {
            CLI::write("  ✗ Found {$totalIssues} validation issues", 'red');
        }
    }

    private function displayCleanupResults(array $cleanup): void
    {
        CLI::write('Cleanup Results:', 'cyan');
        CLI::newLine();

        foreach ($cleanup as $category => $results) {
            if ($category === 'cleanup_log') {
                continue;
            }

            CLI::write("  " . ucfirst(str_replace('_', ' ', $category)) . ":", 'yellow');
            
            if (is_array($results)) {
                foreach ($results as $type => $count) {
                    if ($count > 0) {
                        CLI::write("    - {$type}: {$count} records");
                    }
                }
            }
            CLI::newLine();
        }

        // Display cleanup log
        if (!empty($cleanup['cleanup_log'])) {
            CLI::write('Cleanup Log:', 'cyan');
            foreach ($cleanup['cleanup_log'] as $entry) {
                CLI::write("  {$entry}");
            }
            CLI::newLine();
        }
    }

    private function saveDataQualityReport(array $report): void
    {
        $filename = 'data_quality_report_' . date('Y-m-d_H-i-s') . '.json';
        $filepath = WRITEPATH . 'logs/' . $filename;
        
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        file_put_contents($filepath, json_encode($report, JSON_PRETTY_PRINT));
        
        CLI::write("Data quality report saved to: {$filepath}", 'green');
        
        // Also create a human-readable version
        $txtFilename = str_replace('.json', '.txt', $filename);
        $txtFilepath = WRITEPATH . 'logs/' . $txtFilename;
        
        $this->createReadableReport($report, $txtFilepath);
        CLI::write("Human-readable report saved to: {$txtFilepath}", 'green');
    }

    private function createReadableReport(array $report, string $filepath): void
    {
        $content = "DATA QUALITY REPORT\n";
        $content .= "Generated: " . $report['generated_at'] . "\n";
        $content .= str_repeat("=", 50) . "\n\n";

        // Initial validation results
        $content .= "INITIAL VALIDATION RESULTS\n";
        $content .= str_repeat("-", 30) . "\n";
        
        foreach ($report['validation_results'] as $table => $result) {
            if ($table === 'validation_log' || $table === 'overall_valid') {
                continue;
            }
            
            $content .= ucfirst($table) . ": " . ($result['valid'] ? 'PASSED' : 'FAILED') . "\n";
            
            if (isset($result['total_records'])) {
                $content .= "  Total records: {$result['total_records']}\n";
            }
            
            if (!empty($result['issues'])) {
                $content .= "  Issues found:\n";
                foreach ($result['issues'] as $issue) {
                    $content .= "    - {$issue}\n";
                }
            }
            $content .= "\n";
        }

        // Cleanup results
        $content .= "CLEANUP RESULTS\n";
        $content .= str_repeat("-", 20) . "\n";
        
        foreach ($report['cleanup_results'] as $category => $results) {
            if ($category === 'cleanup_log') {
                continue;
            }
            
            $content .= ucfirst(str_replace('_', ' ', $category)) . ":\n";
            
            if (is_array($results)) {
                foreach ($results as $type => $count) {
                    $content .= "  {$type}: {$count} records\n";
                }
            }
            $content .= "\n";
        }

        // Final validation
        $content .= "FINAL VALIDATION RESULTS\n";
        $content .= str_repeat("-", 25) . "\n";
        
        $finalValid = $report['final_validation']['overall_valid'];
        $content .= "Overall Status: " . ($finalValid ? 'PASSED' : 'FAILED') . "\n\n";
        
        foreach ($report['final_validation'] as $table => $result) {
            if ($table === 'validation_log' || $table === 'overall_valid') {
                continue;
            }
            
            $content .= ucfirst($table) . ": " . ($result['valid'] ? 'PASSED' : 'FAILED') . "\n";
            
            if (!empty($result['issues'])) {
                foreach ($result['issues'] as $issue) {
                    $content .= "  - {$issue}\n";
                }
            }
        }

        file_put_contents($filepath, $content);
    }
}