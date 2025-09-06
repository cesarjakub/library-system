<?php
declare(strict_types=1);

namespace App\Model\Services;

use App\Model\Entities\Book;
use App\Model\Entities\Loan;
use App\Model\Entities\User;
use App\Model\Repositories\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;

class LoanService
{
    private LoanRepository $loanRepository;

    public function __construct(EntityManagerInterface $em)
    {
        /** @var LoanRepository $repo */
        $repo = $em->getRepository(Loan::class);
        $this->loanRepository = $repo;
    }

    public function getAll(): array
    {
        return $this->loanRepository->findAllLoans();
    }

    public function getById(int $id): ?Loan
    {
        return $this->loanRepository->findById($id);
    }

    public function getActiveLoans(): array
    {
        return $this->loanRepository->findBy(['returnedAt' => null]);
    }

    public function create(User $user, Book $book): Loan
    {
        $loan = new Loan($user, $book);
        $this->loanRepository->save($loan);
        return $loan;
    }

    public function delete(Loan $loan): void
    {
        $this->loanRepository->delete($loan);
    }

    public function markReturned(Loan $loan): void
    {
        $loan->setReturnedAt(new \DateTimeImmutable());
        $this->loanRepository->save($loan);
    }
}