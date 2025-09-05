<?php
declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function addUser(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }
}