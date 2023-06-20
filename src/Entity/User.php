<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User extends BaseUser
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    protected $id;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $googleAccessToken = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => false])]
    private ?bool $googleCredentialsAccepted = false;

    public function getGoogleAccessToken(): ?array
    {
        return $this->googleAccessToken;
    }

    public function setGoogleAccessToken(?array $googleAccessToken): self
    {
        $this->googleAccessToken = $googleAccessToken;

        return $this;
    }

    public function getGoogleCredentialsAccepted(): ?bool
    {
        return $this->googleCredentialsAccepted;
    }

    public function setGoogleCredentialsAccepted(?bool $googleCredentialsAccepted): self
    {
        $this->googleCredentialsAccepted = $googleCredentialsAccepted;

        return $this;
    }
}
