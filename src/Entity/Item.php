<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\Table(name: 'item')]
#[ORM\UniqueConstraint(name: 'item_unique', columns: ['line_id', 'audience_id', 'context_id'])]
#[ORM\HasLifecycleCallbacks]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Line::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'line_required')]
    private ?Line $line = null;

    #[ORM\ManyToOne(targetEntity: Audience::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'audience_required')]
    private ?Audience $audience = null;

    #[ORM\ManyToOne(targetEntity: Context::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'context_required')]
    private ?Context $context = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLine(): ?Line
    {
        return $this->line;
    }

    public function setLine(?Line $line): static
    {
        $this->line = $line;
        return $this;
    }

    public function getAudience(): ?Audience
    {
        return $this->audience;
    }

    public function setAudience(?Audience $audience): static
    {
        $this->audience = $audience;
        return $this;
    }

    public function getContext(): ?Context
    {
        return $this->context;
    }

    public function setContext(?Context $context): static
    {
        $this->context = $context;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function __toString(): string
    {
        return trim(sprintf(
            '%s - %s - %s',
            $this->line?->getName(),
            $this->audience?->getName(),
            $this->context?->getName()
        ));
    }
}
