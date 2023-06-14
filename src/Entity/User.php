<?php

namespace App\Entity;

use App\Entity\Traits\EmailTrait;
use App\Enum\UserRoleEnum;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(['email'])]
class User extends AbstractBase implements UserInterface, PasswordAuthenticatedUserInterface
{
    use EmailTrait;

    #[Assert\Email]
    #[ORM\Column(type: Types::STRING, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    private string $password;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: false)]
    private array $roles = [];

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

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
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = UserRoleEnum::ROLE_USER->value; // guarantee every user at least has ROLE_USER

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
