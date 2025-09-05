<?php
declare(strict_types=1);

namespace App\Model\Security;

use App\Model\Entities\User;
use App\Model\Repositories\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\SimpleIdentity;

class UserAuthenticator implements Authenticator
{
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->userRepository = $em->getRepository(User::class);
    }

    function authenticate(string $email, string $password): SimpleIdentity
    {
        $user = $this->userRepository->findByEmail($email);

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