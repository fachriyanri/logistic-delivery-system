<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $attributes = [
        'id_user' => null,
        'username' => null,
        'password' => null,
        'level' => null,
        'is_active' => null,
        'created_at' => null,
        'updated_at' => null,
    ];

    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $datamap = [];

    /**
     * Set password with hashing
     */
    public function setPassword(string $password): self
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_ARGON2ID);
        return $this;
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password']);
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(int $requiredLevel): bool
    {
        return $this->attributes['level'] !== null && $this->attributes['level'] <= $requiredLevel;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->attributes['level'] === USER_LEVEL_ADMIN;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return (bool) ($this->attributes['is_active'] ?? false);
    }

    /**
     * Get user status display
     */
    public function getStatusDisplay(): string
    {
        return $this->isActive() ? 'Active' : 'Inactive';
    }

    /**
     * Get status badge class for display
     */
    public function getStatusBadgeClass(): string
    {
        return $this->isActive() ? 'badge bg-success' : 'badge bg-danger';
    }

    /**
     * Check if user is courier
     */
    public function isCourier(): bool
    {
        return $this->attributes['level'] === USER_LEVEL_COURIER;
    }

    /**
     * Check if user is gudang
     */
    public function isGudang(): bool
    {
        return $this->attributes['level'] === USER_LEVEL_GUDANG;
    }

    /**
     * Get user level name
     */
    public function getLevelName(): string
    {
        $levels = [
            USER_LEVEL_ADMIN => 'Administrator',
            USER_LEVEL_COURIER => 'Kurir',
            USER_LEVEL_GUDANG => 'Gudang'
        ];

        return $levels[$this->attributes['level']] ?? 'Unknown';
    }

    /**
     * Get user display name
     */
    public function getDisplayName(): string
    {
        return $this->attributes['username'] . ' (' . $this->getLevelName() . ')';
    }

    /**
     * Check if user can access admin functions
     */
    public function canAccessAdmin(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can access courier functions
     */
    public function canAccessCourier(): bool
    {
        return $this->isAdmin() || $this->isCourier();
    }

    /**
     * Check if user can access gudang functions
     */
    public function canAccessGudang(): bool
    {
        return $this->isAdmin() || $this->isGudang();
    }
}