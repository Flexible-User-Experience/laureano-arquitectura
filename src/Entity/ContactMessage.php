<?php

namespace App\Entity;

use App\Entity\Traits\EmailTrait;
use App\Entity\Traits\MobileNumberTrait;
use App\Entity\Traits\NameTrait;
use App\Repository\ContactMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactMessageRepository::class)]
class ContactMessage extends AbstractBase
{
    use EmailTrait;
    use MobileNumberTrait;
    use NameTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[Assert\Email]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $email;

    #[ORM\Column(type: Types::TEXT, length: 4000)]
    private string $message;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $hasBeenRead = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $replyDate = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $hasBeenReplied = false;

    #[ORM\Column(type: Types::TEXT, length: 4000, nullable: true)]
    private ?string $replyMessage = null;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getHasBeenRead(): bool
    {
        return $this->hasBeenRead;
    }

    public function setHasBeenRead(bool $hasBeenRead): self
    {
        $this->hasBeenRead = $hasBeenRead;

        return $this;
    }

    public function getReplyDate(): ?\DateTimeInterface
    {
        return $this->replyDate;
    }

    public function getReplyDateString(): string
    {
        return self::convertDateTimeAsString($this->getReplyDate());
    }

    public function setReplyDate(?\DateTimeInterface $replyDate): self
    {
        $this->replyDate = $replyDate;

        return $this;
    }

    public function getHasBeenReplied(): bool
    {
        return $this->hasBeenReplied;
    }

    public function setHasBeenReplied(bool $hasBeenReplied): self
    {
        $this->hasBeenReplied = $hasBeenReplied;

        return $this;
    }

    public function getReplyMessage(): ?string
    {
        return $this->replyMessage;
    }

    public function setReplyMessage(?string $replyMessage): self
    {
        $this->replyMessage = $replyMessage;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getCreatedAtString().' · '.$this->getEmail().' · '.$this->getName() : self::DEFAULT_NULL_STRING;
    }
}
