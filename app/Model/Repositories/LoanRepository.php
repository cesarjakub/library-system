<?php
declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\Loan;
use Doctrine\ORM\EntityRepository;

class LoanRepository extends EntityRepository
{
    public function save(Loan $loan): void
    {
        $this->_em->persist($loan);
        $this->_em->flush();
    }

    public function delete(Loan $loan): void
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
        return $this->createQueryBuilder('l')
            ->orderBy('l.loanedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}