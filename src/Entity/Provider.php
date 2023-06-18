<?php

namespace App\Entity;

use App\Entity\Traits\TotalInvoicedTrait;
use App\Enum\LocaleEnum;
use App\Repository\ProviderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'provider')]
#[UniqueEntity(fields: ['fiscalIdentificationCode'])]
class Provider extends AbstractAccount
{
    use TotalInvoicedTrait;

    #[ORM\Column(type: Types::STRING, length: 20, unique: true)]
    protected string $fiscalIdentificationCode;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $fiscalName;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $commercialName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $address1 = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $address2 = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $postalCode = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $city = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $state = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $country = null;

    #[Assert\Email]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $phoneNumber = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $mobileNumber = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    protected bool $isCompany = true;

    public function __construct()
    {
        $this->locale = LocaleEnum::CA->value;
    }
}
