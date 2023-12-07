<?php

namespace Test;

use Mateodioev\OllamaBot\Models\{User, UserRank};
use Mateodioev\OllamaBot\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    private static UserRepository $userRepository;

    public static function setUpBeforeClass(): void
    {
        self::$userRepository = new MemoryUserRepository();
    }

    public function testCreateUser(): void
    {
        $user = new User(1, 'model', 2);

        $this->assertEquals(1, $user->id);
        $this->assertEquals('model', $user->model);
        $this->assertEquals(UserRank::User, $user->rank);
    }

    public function testFindInvalidUser(): void
    {
        $user = self::$userRepository->find(1);

        $this->assertNull($user);
    }

    public function testSaveUser(): void
    {
        $user = new User(1, 'model', 2);

        self::$userRepository->save($user);

        $this->assertEquals($user, self::$userRepository->find(1));
    }
}
