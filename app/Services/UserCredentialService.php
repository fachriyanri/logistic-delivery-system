<?php

namespace App\Services;

use App\Models\UserModel;
use App\Entities\UserEntity;
use CodeIgniter\Database\BaseConnection;

class UserCredentialService
{
    protected UserModel $userModel;
    protected BaseConnection $db;
    protected array $updateLog = [];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Update existing user accounts with new password hashing
     */
    public function updateExistingUserCredentials(): array
    {
        $this->updateLog = [];
        
        try {
            // Get all existing users
            $existingUsers = $this->userModel->findAll();
            $updated = 0;
            
            foreach ($existingUsers as $user) {
                // Check if password is already using new hashing (length > 32 indicates new hash)
                if (strlen($user->password) <= 32) {
                    // This is likely an old MD5 hash, update it
                    $this->updateUserPassword($user->id_user, 'defaultpassword123');
                    $updated++;
                }
            }
            
            $this->updateLog[] = "Updated {$updated} existing user passwords to secure hashing";
            
            return [
                'success' => true,
                'updated_count' => $updated,
                'log' => $this->updateLog
            ];
            
        } catch (\Exception $e) {
            $this->updateLog[] = "Error updating user credentials: " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * Create default user accounts with specified credentials
     */
    public function createDefaultUsers(): array
    {
        $this->updateLog = [];
        
        $defaultUsers = [
            [
                'id_user' => 'USR01',
                'username' => 'adminpuninar',
                'password' => 'AdminPuninar123',
                'level' => 1
            ],
            [
                'id_user' => 'USR02',
                'username' => 'financepuninar',
                'password' => 'FinancePuninar123',
                'level' => 2
            ],
            [
                'id_user' => 'USR03',
                'username' => 'gudangpuninar',
                'password' => 'GudangPuninar123',
                'level' => 3
            ]
        ];

        $created = 0;
        $updated = 0;

        try {
            foreach ($defaultUsers as $userData) {
                $result = $this->createOrUpdateUser($userData);
                if ($result['created']) {
                    $created++;
                } elseif ($result['updated']) {
                    $updated++;
                }
            }

            $this->updateLog[] = "Created {$created} new default users";
            $this->updateLog[] = "Updated {$updated} existing default users";

            return [
                'success' => true,
                'created_count' => $created,
                'updated_count' => $updated,
                'log' => $this->updateLog
            ];

        } catch (\Exception $e) {
            $this->updateLog[] = "Error creating default users: " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * Set up proper user levels and permissions
     */
    public function setupUserPermissions(): array
    {
        $this->updateLog = [];
        
        try {
            // Ensure all users have valid levels
            $invalidLevels = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user 
                WHERE level NOT IN (1, 2, 3)
            ")->getRow()->count;

            if ($invalidLevels > 0) {
                // Set invalid levels to level 3 (Gudang) as default
                $this->db->query("
                    UPDATE user 
                    SET level = 3 
                    WHERE level NOT IN (1, 2, 3)
                ");
                
                $this->updateLog[] = "Fixed {$invalidLevels} users with invalid permission levels";
            }

            // Ensure admin users exist
            $adminCount = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user 
                WHERE level = 1
            ")->getRow()->count;

            if ($adminCount === 0) {
                $this->updateLog[] = "Warning: No admin users found. Creating default admin user.";
                $this->createOrUpdateUser([
                    'id_user' => 'USR01',
                    'username' => 'adminpuninar',
                    'password' => 'AdminPuninar123',
                    'level' => 1
                ]);
            }

            $this->updateLog[] = "User permission setup completed successfully";

            return [
                'success' => true,
                'log' => $this->updateLog
            ];

        } catch (\Exception $e) {
            $this->updateLog[] = "Error setting up user permissions: " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * Migrate old user data to new format
     */
    public function migrateOldUserData(): array
    {
        $this->updateLog = [];
        
        try {
            // Check if there's old user data to migrate
            if ($this->db->tableExists('user_backup')) {
                $oldUsers = $this->db->table('user_backup')->get()->getResultArray();
                
                $migrated = 0;
                foreach ($oldUsers as $oldUser) {
                    $newUserData = [
                        'id_user' => $oldUser['id_user'],
                        'username' => $oldUser['username'],
                        'password' => 'defaultpassword123', // Reset to default secure password
                        'level' => (int)$oldUser['level']
                    ];
                    
                    $result = $this->createOrUpdateUser($newUserData);
                    if ($result['created'] || $result['updated']) {
                        $migrated++;
                    }
                }
                
                $this->updateLog[] = "Migrated {$migrated} users from backup table";
            } else {
                $this->updateLog[] = "No user backup table found, skipping migration";
            }

            return [
                'success' => true,
                'log' => $this->updateLog
            ];

        } catch (\Exception $e) {
            $this->updateLog[] = "Error migrating old user data: " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * Validate all user credentials
     */
    public function validateUserCredentials(): array
    {
        $issues = [];
        
        try {
            // Check for users with empty usernames
            $emptyUsernames = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user 
                WHERE username IS NULL OR username = '' OR TRIM(username) = ''
            ")->getRow()->count;
            
            if ($emptyUsernames > 0) {
                $issues[] = "Found {$emptyUsernames} users with empty usernames";
            }

            // Check for users with weak passwords (old MD5 hashes)
            $weakPasswords = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user 
                WHERE LENGTH(password) <= 32
            ")->getRow()->count;
            
            if ($weakPasswords > 0) {
                $issues[] = "Found {$weakPasswords} users with weak/old password hashing";
            }

            // Check for duplicate usernames
            $duplicateUsernames = $this->db->query("
                SELECT username, COUNT(*) as count 
                FROM user 
                GROUP BY username 
                HAVING COUNT(*) > 1
            ")->getResultArray();
            
            if (!empty($duplicateUsernames)) {
                $issues[] = "Found " . count($duplicateUsernames) . " duplicate usernames";
            }

            // Check for invalid user levels
            $invalidLevels = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user 
                WHERE level NOT IN (1, 2, 3)
            ")->getRow()->count;
            
            if ($invalidLevels > 0) {
                $issues[] = "Found {$invalidLevels} users with invalid permission levels";
            }

            return [
                'valid' => empty($issues),
                'issues' => $issues,
                'total_users' => $this->userModel->countAllResults()
            ];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'issues' => ['Validation failed: ' . $e->getMessage()],
                'total_users' => 0
            ];
        }
    }

    /**
     * Create or update a user
     */
    private function createOrUpdateUser(array $userData): array
    {
        $existing = $this->userModel->find($userData['id_user']);
        
        if ($existing) {
            // Update existing user
            $existing->username = $userData['username'];
            $existing->setPassword($userData['password']);
            $existing->level = $userData['level'];
            
            $this->userModel->save($existing);
            
            $this->updateLog[] = "Updated user: {$userData['username']} (ID: {$userData['id_user']})";
            
            return ['created' => false, 'updated' => true];
        } else {
            // Create new user
            $user = new UserEntity();
            $user->id_user = $userData['id_user'];
            $user->username = $userData['username'];
            $user->setPassword($userData['password']);
            $user->level = $userData['level'];
            
            $this->userModel->save($user);
            
            $this->updateLog[] = "Created user: {$userData['username']} (ID: {$userData['id_user']})";
            
            return ['created' => true, 'updated' => false];
        }
    }

    /**
     * Update user password with secure hashing
     */
    private function updateUserPassword(string $userId, string $newPassword): bool
    {
        $user = $this->userModel->find($userId);
        
        if ($user) {
            $user->setPassword($newPassword);
            return $this->userModel->save($user);
        }
        
        return false;
    }

    /**
     * Get update log
     */
    public function getUpdateLog(): array
    {
        return $this->updateLog;
    }

    /**
     * Complete user credential update process
     */
    public function completeCredentialUpdate(): array
    {
        $this->updateLog = [];
        
        try {
            // Step 1: Migrate old user data if exists
            $migration = $this->migrateOldUserData();
            
            // Step 2: Update existing user credentials
            $credentialUpdate = $this->updateExistingUserCredentials();
            
            // Step 3: Create default users
            $defaultUsers = $this->createDefaultUsers();
            
            // Step 4: Setup permissions
            $permissions = $this->setupUserPermissions();
            
            // Step 5: Validate final state
            $validation = $this->validateUserCredentials();

            return [
                'success' => true,
                'migration' => $migration,
                'credential_update' => $credentialUpdate,
                'default_users' => $defaultUsers,
                'permissions' => $permissions,
                'validation' => $validation,
                'complete_log' => $this->updateLog
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'log' => $this->updateLog
            ];
        }
    }
}