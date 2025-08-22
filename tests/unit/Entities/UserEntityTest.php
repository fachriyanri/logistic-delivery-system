<?php

namespace Tests\Unit\Entities;

use App\Entities\UserEntity;
use CodeIgniter\Test\CIUnitTestCase;

class UserEntityTest extends CIUnitTestCase
{
    private UserEntity $userEntity;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userEntity = new UserEntity();
    }

    public function testCanSetAndGetBasicProperties(): void
    {
        $data = [
            'id_user' => 'USR001',
            'username' => 'testuser',
            'level' => 1,
        ];

        $this->userEntity->fill($data);

        $this->assertEquals('USR001', $this->userEntity->id_user);
        $this->assertEquals('testuser', $this->userEntity->username);
        $this->assertEquals(1, $this->userEntity->level);
    }

    public function testSetPasswordHashesPassword(): void
    {
        $plainPassword = 'testpassword123';
        $this->userEntity->setPassword($plainPassword);

        $this->assertNotEquals($plainPassword, $this->userEntity->password);
        $this->assertTrue(password_verify($plainPassword, $this->userEntity->password));
    }

    public function testVerifyPasswordWorksCorrectly(): void
    {
        $plainPassword = 'testpassword123';
        $this->userEntity->setPassword($plainPassword);

        $this->assertTrue($this->userEntity->verifyPassword($plainPassword));
        $this->assertFalse($this->userEntity->verifyPassword('wrongpassword'));
    }

    public function testHasRoleForAdmin(): void
    {
        $this->userEntity->level = 1;
        
        $this->assertTrue($this->userEntity->hasRole('admin'));
        $this->assertTrue($this->userEntity->hasRole('finance'));
        $this->assertTrue($this->userEntity->hasRole('gudang'));
    }

    public function testHasRoleForFinance(): void
    {
        $this->userEntity->level = 2;
        
        $this->assertFalse($this->userEntity->hasRole('admin'));
        $this->assertTrue($this->userEntity->hasRole('finance'));
        $this->assertTrue($this->userEntity->hasRole('gudang'));
    }

    public function testHasRoleForGudang(): void
    {
        $this->userEntity->level = 3;
        
        $this->assertFalse($this->userEntity->hasRole('admin'));
        $this->assertFalse($this->userEntity->hasRole('finance'));
        $this->assertTrue($this->userEntity->hasRole('gudang'));
    }

    public function testIsAdminMethod(): void
    {
        $this->userEntity->level = 1;
        $this->assertTrue($this->userEntity->isAdmin());

        $this->userEntity->level = 2;
        $this->assertFalse($this->userEntity->isAdmin());

        $this->userEntity->level = 3;
        $this->assertFalse($this->userEntity->isAdmin());
    }

    public function testGetLevelNameMethod(): void
    {
        $this->userEntity->level = 1;
        $this->assertEquals('Admin', $this->userEntity->getLevelName());

        $this->userEntity->level = 2;
        $this->assertEquals('Finance', $this->userEntity->getLevelName());

        $this->userEntity->level = 3;
        $this->assertEquals('Gudang', $this->userEntity->getLevelName());

        $this->userEntity->level = 99;
        $this->assertEquals('Unknown', $this->userEntity->getLevelName());
    }

    public function testDateTimeCasting(): void
    {
        $now = date('Y-m-d H:i:s');
        $this->userEntity->created_at = $now;
        $this->userEntity->updated_at = $now;

        $this->assertInstanceOf(\CodeIgniter\I18n\Time::class, $this->userEntity->created_at);
        $this->assertInstanceOf(\CodeIgniter\I18n\Time::class, $this->userEntity->updated_at);
    }

    public function testToArrayExcludesPassword(): void
    {
        $data = [
            'id_user' => 'USR001',
            'username' => 'testuser',
            'password' => 'hashedpassword',
            'level' => 1,
        ];

        $this->userEntity->fill($data);
        $array = $this->userEntity->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayHasKey('id_user', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('level', $array);
    }
}