<?php
declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function saveUser(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function removeUser(User $user): void
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    public function getUser(int $id): object
    {
        return $this->find($id);
    }

    public function getAllUsers(): array
    {
        return $this->findAll();
    }

    public function findUserByEmail(string $email): object
    {
        return $this->findOneBy(['email' => $email]);
    }
}