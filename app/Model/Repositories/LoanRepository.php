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

    public function deleteLoan(Loan $loan): void
    {
        $this->_em->remove($loan);
        $this->_em->flush();
    }

    public function findById(int $id): ?Loan
    {
        return $this->find($id);
    }

    public function findAllLoans(): array
    {
        return parent::findAll();
    }

    public function markReturned(Loan $loan): void
    {
        $loan->setReturnedAt(new \DateTimeImmutable());
        $this->_em->flush();
    }
}