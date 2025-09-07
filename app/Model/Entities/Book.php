<?php
declare(strict_types=1);

namespace App\Model\Entities;

use App\Model\Repositories\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'books')]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'string')]
    private string $author;

    #[ORM\Column(type: 'integer')]
    private int $year;

    #[ORM\Column(type: 'string')]
    private string $isbn;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $coverPath = null;

    public function __construct(string $title, string $author, int $year, string $isbn)
    {
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->isbn = $isbn;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): void
    {
        $this->isbn = $isbn;
    }


    public function getCoverPath(): ?string
    {
        return $this->coverPath;
    }

    public function setCoverPath(?string $coverPath): void
    {
        $this->coverPath = $coverPath;
    }

}
