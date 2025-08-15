<?php

namespace App\Entity;

use App\Repository\ItemCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemCategoryRepository::class)]
#[ORM\Table(name: 'item_category')]
#[ORM\UniqueConstraint(name: 'item_category_unique', columns: ['item_id', 'segment', 'category_id', 'subject_id'])]
#[ORM\HasLifecycleCallbacks]
class ItemCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'itemCategories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'item_required')]
    private ?Item $item = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'segment_required')]
    #[Assert\Choice(choices: ['components', 'modules', 'fasteners', 'enclosures', 'accessories'], message: 'invalid_segment')]
    private ?string $segment = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Subject $subject = null;

    #[ORM\OneToMany(mappedBy: 'itemCategory', targetEntity: ItemCategoryAttributeValue::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attributeValues;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sortOrder = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->attributeValues = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;
        return $this;
    }

    public function getSegment(): ?string
    {
        return $this->segment;
    }

    public function setSegment(string $segment): static
    {
        $this->segment = $segment;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getAttributeValues(): Collection
    {
        return $this->attributeValues;
    }

    public function addAttributeValue(ItemCategoryAttributeValue $attributeValue): static
    {
        if (!$this->attributeValues->contains($attributeValue)) {
            $this->attributeValues->add($attributeValue);
            $attributeValue->setItemCategory($this);
        }

        return $this;
    }

    public function removeAttributeValue(ItemCategoryAttributeValue $attributeValue): static
    {
        if ($this->attributeValues->removeElement($attributeValue)) {
            if ($attributeValue->getItemCategory() === $this) {
                $attributeValue->setItemCategory(null);
            }
        }

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

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getLabel(): string
    {
        if ($this->subject) {
            return $this->subject->getName();
        }

        if ($this->category) {
            return $this->category->getName();
        }

        return 'unknown';
    }

    public function getPath(): string
    {
        if ($this->subject) {
            return $this->subject->getCategory()
                ? $this->subject->getCategory()->getPathString() . ' / ' . $this->subject->getName()
                : $this->subject->getName();
        }

        if ($this->category) {
            return $this->category->getPathString();
        }

        return 'unknown';
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}
