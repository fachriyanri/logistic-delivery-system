<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\UserEntity;

/**
 * User Model
 * 
 * Data access layer for user management in the logistics system.
 * Handles CRUD operations, authentication, validation, and user-related
 * database operations with proper security measures.
 * 
 * @package App\Models
 * @author  CodeIgniter Logistics System
 * @version 1.0.0
 * @since   2024-01-01
 * 
 * @property string $table           Database table name
 * @property string $primaryKey      Primary key field
 * @property string $returnType      Return type for queries
 * @property array  $allowedFields   Fields allowed for mass assignment
 * @property array  $validationRules Validation rules for data integrity
 */
class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id_user';
    protected $returnType = UserEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_user',
        'username',
        'password',
        'level',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'id_user' => [
            'rules' => 'required|max_length[5]|is_unique[user.id_user,id_user,{id_user}]',
            'errors' => [
                'required' => 'User ID is required',
                'max_length' => 'User ID cannot exceed 5 characters',
                'is_unique' => 'User ID already exists'
            ]
        ],
        'username' => [
            'rules' => 'required|min_length[3]|max_length[50]|is_unique[user.username,id_user,{id_user}]',
            'errors' => [
                'required' => 'Username is required',
                'min_length' => 'Username must be at least 3 characters',
                'max_length' => 'Username cannot exceed 50 characters',
                'is_unique' => 'Username already exists'
            ]
        ],
        'password' => [
            'rules' => 'required|min_length[6]',
            'errors' => [
                'required' => 'Password is required',
                'min_length' => 'Password must be at least 6 characters'
            ]
        ],
        'level' => [
            'rules' => 'required|in_list[1,2,3]',
            'errors' => [
                'required' => 'User level is required',
                'in_list' => 'Invalid user level'
            ]
        ],
        'is_active' => [
            'rules' => 'permit_empty|in_list[0,1]',
            'errors' => [
                'in_list' => 'Invalid active status'
            ]
        ]
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Generate next user ID
     * 
     * Generates the next sequential user ID in format USRnn (e.g., USR01, USR02).
     * Finds the highest existing ID and increments by 1.
     * 
     * @return string Next available user ID
     * 
     * @example
     * // Generate next ID
     * $nextId = $userModel->generateNextId(); // Returns "USR04" if USR03 exists
     * 
     * @see str_pad() For zero-padding the number
     */
    public function generateNextId(): string
    {
        $lastUser = $this->orderBy('id_user', 'DESC')->first();
        
        if (!$lastUser) {
            return 'USR01';
        }

        $lastNumber = (int) substr($lastUser->id_user, 3);
        $nextNumber = $lastNumber + 1;

        return 'USR' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get user by username
     * 
     * Retrieves a user entity by username for authentication purposes.
     * Returns null if user is not found.
     * 
     * @param string $username The username to search for
     * 
     * @return UserEntity|null User entity if found, null otherwise
     * 
     * @example
     * // Find user for authentication
     * $user = $userModel->getUserByUsername('adminpuninar');
     * if ($user && password_verify($password, $user->password)) {
     *     // Authentication successful
     * }
     * 
     * @see UserEntity User entity class
     */
    public function getUserByUsername(string $username): ?UserEntity
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Get users by level
     */
    public function getUsersByLevel(int $level): array
    {
        return $this->where('level', $level)->findAll();
    }

    /**
     * Update user password
     * 
     * Updates user password with secure hashing using Argon2ID algorithm.
     * Automatically hashes the password before storing in database.
     * 
     * @param string $userId      The user ID to update
     * @param string $newPassword The new plain text password
     * 
     * @return bool True if update successful, false otherwise
     * 
     * @example
     * // Update user password
     * $success = $userModel->updatePassword('USR01', 'NewSecurePassword123');
     * 
     * @see password_hash() For secure password hashing
     * @see PASSWORD_ARGON2ID Hashing algorithm constant
     */
    public function updatePassword(string $userId, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
        
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Check if username exists (excluding current user)
     */
    public function usernameExists(string $username, ?string $excludeUserId = null): bool
    {
        $query = $this->where('username', $username);
        
        if ($excludeUserId) {
            $query->where('id_user !=', $excludeUserId);
        }
        
        return $query->countAllResults() > 0;
    }

    /**
     * Get user level name
     */
    public function getLevelName(int $level): string
    {
        $levels = [
            USER_LEVEL_ADMIN => 'Administrator',
            USER_LEVEL_FINANCE => 'Finance',
            USER_LEVEL_GUDANG => 'Gudang'
        ];

        return $levels[$level] ?? 'Unknown';
    }

    /**
     * Get all users excluding admin (level 1) for user management
     */
    public function getNonAdminUsers(): array
    {
        return $this->where('level !=', USER_LEVEL_ADMIN)
                   ->orderBy('username', 'ASC')
                   ->findAll();
    }

    /**
     * Toggle user active status
     */
    public function toggleActiveStatus(string $userId): bool
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        $newStatus = $user->is_active ? 0 : 1;
        return $this->update($userId, ['is_active' => $newStatus]);
    }

    /**
     * Update user active status
     */
    public function updateActiveStatus(string $userId, bool $isActive): bool
    {
        return $this->update($userId, ['is_active' => $isActive ? 1 : 0]);
    }

    /**
     * Check if user can be deleted (not admin and not in use)
     */
    public function canBeDeleted(string $userId): bool
    {
        $user = $this->find($userId);
        if (!$user || $user->level === USER_LEVEL_ADMIN) {
            return false;
        }

        // Add additional checks here if needed (e.g., user has no related records)
        return true;
    }
}