<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\UserCredentialService;

class UpdateUserCredentials extends BaseCommand
{
    protected $group       = 'Migration';
    protected $name        = 'update:user-credentials';
    protected $description = 'Update user credentials and create default users';
    protected $usage       = 'update:user-credentials [options]';
    protected $arguments   = [];
    protected $options     = [
        '--create-defaults' => 'Create default user accounts',
        '--update-existing' => 'Update existing user password hashing',
        '--setup-permissions' => 'Setup user permission levels',
        '--validate' => 'Validate user credentials',
        '--complete' => 'Run complete credential update process',
    ];

    public function run(array $params)
    {
        $createDefaults = CLI::getOption('create-defaults') !== null;
        $updateExisting = CLI::getOption('update-existing') !== null;
        $setupPermissions = CLI::getOption('setup-permissions') !== null;
        $validate = CLI::getOption('validate') !== null;
        $complete = CLI::getOption('complete') !== null;

        CLI::write('Starting user credential update...', 'green');
        CLI::newLine();

        $credentialService = new UserCredentialService();

        try {
            if ($complete) {
                // Run complete process
                CLI::write('Running complete credential update process...', 'yellow');
                $result = $credentialService->completeCredentialUpdate();
                
                if ($result['success']) {
                    $this->displayCompleteResults($result);
                } else {
                    CLI::error('Complete update failed: ' . $result['error']);
                    $this->displayLog($result['log']);
                }
            } else {
                // Run individual operations
                if ($updateExisting) {
                    CLI::write('Updating existing user credentials...', 'yellow');
                    $result = $credentialService->updateExistingUserCredentials();
                    $this->displayOperationResult('Credential Update', $result);
                }

                if ($createDefaults) {
                    CLI::write('Creating default user accounts...', 'yellow');
                    $result = $credentialService->createDefaultUsers();
                    $this->displayOperationResult('Default Users Creation', $result);
                }

                if ($setupPermissions) {
                    CLI::write('Setting up user permissions...', 'yellow');
                    $result = $credentialService->setupUserPermissions();
                    $this->displayOperationResult('Permission Setup', $result);
                }

                if ($validate) {
                    CLI::write('Validating user credentials...', 'yellow');
                    $validation = $credentialService->validateUserCredentials();
                    $this->displayValidationResults($validation);
                }
            }

            CLI::newLine();
            CLI::write('User credential update completed!', 'green');

        } catch (\Exception $e) {
            CLI::error('Credential update failed: ' . $e->getMessage());
            
            // Display any partial log
            $log = $credentialService->getUpdateLog();
            if (!empty($log)) {
                CLI::newLine();
                CLI::write('Partial update log:', 'yellow');
                $this->displayLog($log);
            }
        }
    }

    private function displayCompleteResults(array $result): void
    {
        CLI::write('Complete Credential Update Results:', 'cyan');
        CLI::newLine();

        // Migration results
        if (isset($result['migration'])) {
            CLI::write('Migration:', 'yellow');
            $this->displayLog($result['migration']['log']);
            CLI::newLine();
        }

        // Credential update results
        if (isset($result['credential_update'])) {
            CLI::write('Credential Update:', 'yellow');
            CLI::write("  Updated passwords: {$result['credential_update']['updated_count']}");
            $this->displayLog($result['credential_update']['log']);
            CLI::newLine();
        }

        // Default users results
        if (isset($result['default_users'])) {
            CLI::write('Default Users:', 'yellow');
            CLI::write("  Created: {$result['default_users']['created_count']}");
            CLI::write("  Updated: {$result['default_users']['updated_count']}");
            $this->displayLog($result['default_users']['log']);
            CLI::newLine();
        }

        // Permission setup results
        if (isset($result['permissions'])) {
            CLI::write('Permission Setup:', 'yellow');
            $this->displayLog($result['permissions']['log']);
            CLI::newLine();
        }

        // Final validation
        if (isset($result['validation'])) {
            CLI::write('Final Validation:', 'yellow');
            $this->displayValidationResults($result['validation']);
        }
    }

    private function displayOperationResult(string $operation, array $result): void
    {
        CLI::write("{$operation} Results:", 'cyan');
        
        if ($result['success']) {
            CLI::write('  Status: Success', 'green');
            
            if (isset($result['created_count'])) {
                CLI::write("  Created: {$result['created_count']}");
            }
            
            if (isset($result['updated_count'])) {
                CLI::write("  Updated: {$result['updated_count']}");
            }
            
            $this->displayLog($result['log']);
        } else {
            CLI::write('  Status: Failed', 'red');
            if (isset($result['error'])) {
                CLI::write("  Error: {$result['error']}", 'red');
            }
        }
        
        CLI::newLine();
    }

    private function displayValidationResults(array $validation): void
    {
        if ($validation['valid']) {
            CLI::write('  ✓ All user credentials are valid', 'green');
            CLI::write("  Total users: {$validation['total_users']}");
        } else {
            CLI::write('  ✗ User credential validation failed', 'red');
            CLI::write("  Total users: {$validation['total_users']}");
            CLI::write('  Issues found:', 'yellow');
            
            foreach ($validation['issues'] as $issue) {
                CLI::write("    - {$issue}", 'red');
            }
        }
        
        CLI::newLine();
    }

    private function displayLog(array $log): void
    {
        foreach ($log as $entry) {
            CLI::write("  - {$entry}");
        }
    }
}