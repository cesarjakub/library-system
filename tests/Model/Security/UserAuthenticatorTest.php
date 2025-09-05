<?php
declare(strict_types=1);

namespace Model\Security;

use App\Model\Entities\User;
use App\Model\Repositories\UserRepository;
use App\Model\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Nette\Security\AuthenticationException;
use Nette\Security\SimpleIdentity;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class UserAuthenticatorTest extends TestCase
{
    private UserAuthenticator $authenticator;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $em = Mockery::mock(EntityManagerInterface::class);
        $this->userRepository = Mockery::mock(UserRepository::class);

        $em->shouldReceive('getRepository')
            ->with(User::class)
            ->andReturn($this->userRepository);

        $this->authenticator = new UserAuthenticator($em);
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

        $this->userRepository->shouldReceive('findByEmail')
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
        $user->shouldReceive('verifyPassword')->with('pass')->andReturn(false);

        $this->userRepository->shouldReceive('findByEmail')
            ->with('john@example.com')
            ->andReturn($user);

        Assert::exception(
            fn() => $this->authenticator->authenticate('john@example.com', 'pass'),
            AuthenticationException::class
        );
    }
}

(new UserAuthenticatorTest())->run();