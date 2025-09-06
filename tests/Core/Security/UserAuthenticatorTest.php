<?php
declare(strict_types=1);

namespace Core\Security;

use App\Core\Security\UserAuthenticator;
use App\Model\Entities\User;
use App\Model\Services\UserService;
use Mockery;
use Nette\Security\AuthenticationException;
use Nette\Security\SimpleIdentity;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class UserAuthenticatorTest extends TestCase
{
    private UserAuthenticator $authenticator;
    private UserService $userServiceMock;

    protected function setUp(): void
    {
        $this->userServiceMock = Mockery::mock(UserService::class);

        $this->authenticator = new UserAuthenticator($this->userServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testAuthenticateWithValidCredentials(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('verifyPassword')->with('pass')->andReturn(true);
        $user->shouldReceive('getId')->andReturn(123);
        $user->shouldReceive('getRole')->andReturn('admin');
        $user->shouldReceive('getEmail')->andReturn('john@example.com');

        $this->userServiceMock
            ->shouldReceive('getUserByEmail')
            ->with('john@example.com')
            ->andReturn($user);

        $identity = $this->authenticator->authenticate('john@example.com', 'pass');

        Assert::type(SimpleIdentity::class, $identity);
        Assert::same(123, $identity->getId());
        Assert::same('admin', $identity->getRoles()[0]);
        Assert::same('john@example.com', $identity->getData()['email']);
    }

    public function testAuthenticateWithInvalidCredentials(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('verifyPassword')->with('wrongpass')->andReturn(false);

        $this->userServiceMock
            ->shouldReceive('getUserByEmail')
            ->with('john@example.com')
            ->andReturn($user);

        Assert::exception(
            fn() => $this->authenticator->authenticate('john@example.com', 'wrongpass'),
            AuthenticationException::class
        );
    }

    public function testAuthenticateWithNonexistentUser(): void
    {
        $this->userServiceMock
            ->shouldReceive('getUserByEmail')
            ->with('unknown@example.com')
            ->andReturn(null);

        Assert::exception(
            fn() => $this->authenticator->authenticate('unknown@example.com', 'pass'),
            AuthenticationException::class
        );
    }
}

(new UserAuthenticatorTest())->run();