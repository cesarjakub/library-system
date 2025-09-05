<?php
declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function save(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function delete(User $user): void
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    public function findById(int $id): ?User
    {
        return $this->find($id);
    }

    public function findAllUsers(): array
    {
        return $this->findAll();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }
}