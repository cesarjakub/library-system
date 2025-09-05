<?php
declare(strict_types=1);

namespace App\Core\Security;

use App\Model\Services\UserService;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\SimpleIdentity;

class UserAuthenticator implements Authenticator
{
    public function __construct(
        private UserService $userService
    ){}

    function authenticate(string $email, string $password): SimpleIdentity
    {
        $user = $this->userService->getUserByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        return new SimpleIdentity(
            $user->getId(),
            $user->getRole(),
            ['email' => $user->getEmail()]
        );
    }
}