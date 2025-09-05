<?php
declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\Loan;
use Doctrine\ORM\EntityRepository;

class LoanRepository extends EntityRepository
{
    public function saveLoan(Loan $loan): void
    {
        $this->_em->persist($loan);
        $this->_em->flush();
    }

    public function removeLoan(Loan $loan): void
    {
        $this->_em->remove($loan);
        $this->_em->flush();
    }

    public function getLoan(int $id): object
    {
        return $this->find($id);
    }

    public function getAllLoans(): array
    {
        return $this->findAll();
    }

    public function markReturned(Loan $loan): void
    {
        $loan->setReturnedAt(new \DateTimeImmutable());
        $this->_em->flush();
    }
}