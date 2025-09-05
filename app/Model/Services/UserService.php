<?php
declare(strict_types=1);

namespace App\Model\Services;

use App\Model\Entities\User;
use App\Model\Repositories\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var UserRepository $repo */
        $repo = $em->getRepository(User::class);
        $this->userRepository = $repo;
    }

    public function getAll(): array
    {
        return $this->userRepository->findAllUsers();
    }

    public function getById(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByEmail(string $email): ?object
    {
        return $this->userRepository->findByEmail($email);
    }

    public function create(string $email, string $password): User
    {
        $user = new User($email, $password);
        $this->userRepository->saveUser($user);
        return $user;
    }

    public function delete(User $user): void
    {
        $this->userRepository->removeUser($user);
    }
}