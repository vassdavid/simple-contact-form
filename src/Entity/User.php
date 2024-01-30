<?php

namespace App\Entity;

use App\Constant\UserRole;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 30)]
    #[ORM\Column(type: Types::STRING, length: 30, unique: true)]   
    private ?string $username;

    /** @var array<int,string> */
    #[Assert\Type('array')]
    #[Assert\Choice(UserRole::ROLES, multiple: true)]
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank()]
    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = UserRole::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * @param array<int,string> $roles
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return  self
     */ 
    public function setUsername(?string $username)
    {
        $this->username = $username;

        return $this;
    }
}
