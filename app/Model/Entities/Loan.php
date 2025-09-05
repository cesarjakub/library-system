<?php
declare(strict_types=1);

namespace App\Model\Entities;

use App\Model\Repositories\LoanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Book::class)]
    private Book $book;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $loanedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $returnedAt = null;

    public function __construct(User $user, Book $book)
    {
        $this->user = $user;
        $this->book = $book;
        $this->loanedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): void
    {
        $this->book = $book;
    }

    public function getLoanedAt(): \DateTimeImmutable
    {
        return $this->loanedAt;
    }

    public function setLoanedAt(\DateTimeImmutable $loanedAt): void
    {
        $this->loanedAt = $loanedAt;
    }

    public function getReturnedAt(): ?\DateTimeImmutable
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTimeImmutable $returnedAt): void
    {
        $this->returnedAt = $returnedAt;
    }

}
