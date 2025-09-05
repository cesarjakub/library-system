<?php
declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\Book;
use App\Model\Entities\Loan;
use App\Model\Entities\User;
use Doctrine\ORM\EntityRepository;

class LoanRepository extends EntityRepository
{
    public function findActiveLoans(): array
    {
        return $this->findAll();
    }

    public function createLoan(User $user, Book $book): void
    {
        $loan = new Loan($user, $book);

        $this->_em->persist($loan);
        $this->_em->flush();
    }

    public function markReturned(Loan $loan): void
    {
        $loan->setReturnedAt(new \DateTimeImmutable());
        $this->_em->flush();
    }
}