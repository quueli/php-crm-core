<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: ItemCategory::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $itemCategories;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->itemCategories = new ArrayCollection();
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

    public function getDisplayName(): string
    {
        $parts = [];
        if ($this->line) {
            $parts[] = $this->line->getName();
        }
        if ($this->audience) {
            $parts[] = $this->audience->getName();
        }
        if ($this->context) {
            $parts[] = $this->context->getName();
        }

        return implode(' - ', $parts);
    }

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    /**
     * @return Collection<int, ItemCategory>
     */
    public function getItemCategories(): Collection
    {
        return $this->itemCategories;
    }

    public function addItemCategory(ItemCategory $itemCategory): static
    {
        if (!$this->itemCategories->contains($itemCategory)) {
            $this->itemCategories->add($itemCategory);
            $itemCategory->setItem($this);
        }

        return $this;
    }

    public function removeItemCategory(ItemCategory $itemCategory): static
    {
        if ($this->itemCategories->removeElement($itemCategory)) {
            if ($itemCategory->getItem() === $this) {
                $itemCategory->setItem(null);
            }
        }

        return $this;
    }

    public function getItemCategoriesForSegment(string $segment): array
    {
        return $this->itemCategories->filter(
            fn (ItemCategory $itemCategory) => $itemCategory->getSegment() === $segment
        )->toArray();
    }
}
