<?php

namespace Tests\Unit\Models;

use App\Models\UserModel;
use App\Entities\UserEntity;
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
            'id_user' => 'USR999',
            'username' => 'newuser',
            'password' => password_hash('password123', PASSWORD_ARGON2ID),
            'level' => 2,
        ];

        $result = $this->userModel->insert($userData);
        $this->assertTrue($result);

        $user = $this->userModel->find('USR999');
        $this->assertInstanceOf(UserEntity::class, $user);
        $this->assertEquals('newuser', $user->username);
        $this->assertEquals(2, $user->level);
    }

    public function testCanUpdateUser(): void
    {
        $updateData = [
            'username' => 'updatedadmin',
            'level' => 1,
        ];

        $result = $this->userModel->update('USR01', $updateData);
        $this->assertTrue($result);

        $user = $this->userModel->find('USR01');
        $this->assertEquals('updatedadmin', $user->username);
    }

    public function testCanDeleteUser(): void
    {
        $result = $this->userModel->delete('USR03');
        $this->assertTrue($result);

        $user = $this->userModel->find('USR03');
        $this->assertNull($user);
    }

    public function testFindByUsernameMethod(): void
    {
        $user = $this->userModel->findByUsername('testadmin');
        
        $this->assertInstanceOf(UserEntity::class, $user);
        $this->assertEquals('testadmin', $user->username);
        $this->assertEquals('USR01', $user->id_user);
    }

    public function testFindByUsernameReturnsNullForNonExistentUser(): void
    {
        $user = $this->userModel->findByUsername('nonexistent');
        $this->assertNull($user);
    }

    public function testGenerateNextIdMethod(): void
    {
        $nextId = $this->userModel->generateNextId();
        
        $this->assertIsString($nextId);
        $this->assertStringStartsWith('USR', $nextId);
        $this->assertEquals(5, strlen($nextId));
    }

    public function testGetUsersByLevelMethod(): void
    {
        $adminUsers = $this->userModel->getUsersByLevel(1);
        $this->assertIsArray($adminUsers);
        $this->assertCount(1, $adminUsers);
        $this->assertEquals('testadmin', $adminUsers[0]->username);

        $kurirUsers = $this->userModel->getUsersByLevel(2);
        $this->assertIsArray($kurirUsers);
        $this->assertCount(1, $kurirUsers);
        $this->assertEquals('testkurir', $kurirUsers[0]->username);
    }

    public function testValidationRules(): void
    {
        $validationRules = $this->userModel->getValidationRules();
        
        $this->assertArrayHasKey('id_user', $validationRules);
        $this->assertArrayHasKey('username', $validationRules);
        $this->assertArrayHasKey('password', $validationRules);
        $this->assertArrayHasKey('level', $validationRules);
    }

    public function testValidationFailsForDuplicateUsername(): void
    {
        $userData = [
            'id_user' => 'USR999',
            'username' => 'testadmin', // Duplicate username
            'password' => password_hash('password123', PASSWORD_ARGON2ID),
            'level' => 2,
        ];

        $result = $this->userModel->insert($userData);
        $this->assertFalse($result);
        
        $errors = $this->userModel->errors();
        $this->assertNotEmpty($errors);
    }

    public function testValidationFailsForInvalidLevel(): void
    {
        $userData = [
            'id_user' => 'USR999',
            'username' => 'newuser',
            'password' => password_hash('password123', PASSWORD_ARGON2ID),
            'level' => 5, // Invalid level
        ];

        $result = $this->userModel->insert($userData);
        $this->assertFalse($result);
        
        $errors = $this->userModel->errors();
        $this->assertNotEmpty($errors);
    }

    public function testCanGetAllUsersWithPagination(): void
    {
        $users = $this->userModel->paginate(10);
        
        $this->assertIsArray($users);
        $this->assertCount(3, $users); // We have 3 test users
        
        foreach ($users as $user) {
            $this->assertInstanceOf(UserEntity::class, $user);
        }
    }

    public function testCanSearchUsersByUsername(): void
    {
        $users = $this->userModel->searchByUsername('test');
        
        $this->assertIsArray($users);
        $this->assertCount(3, $users); // All test users contain 'test'
        
        $users = $this->userModel->searchByUsername('admin');
        $this->assertCount(1, $users);
        $this->assertEquals('testadmin', $users[0]->username);
    }
}