<?php
declare(strict_types=1);

namespace App\Model\Entities;

use App\Model\Repositories\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string', options: ['default' => 'user'])]
    private string $role = self::ROLE_USER;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER  = 'user';

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->setPassword($password);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        if (!in_array($role, [self::ROLE_ADMIN, self::ROLE_USER], true)) {
            throw new \InvalidArgumentException("Invalid role: $role");
        }
        $this->role = $role;
    }

}