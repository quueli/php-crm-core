<?php

namespace App\Entity;

use App\Repository\ItemCategoryAttributeValueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemCategoryAttributeValueRepository::class)]
#[ORM\Table(name: 'item_category_attribute_value')]
#[ORM\UniqueConstraint(name: 'item_category_attribute_value_unique', columns: ['item_category_id', 'attribute_value_id'])]
#[ORM\HasLifecycleCallbacks]
class ItemCategoryAttributeValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ItemCategory::class, inversedBy: 'attributeValues')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'item_category_required')]
    private ?ItemCategory $itemCategory = null;

    #[ORM\ManyToOne(targetEntity: AttributeValue::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'attribute_value_required')]
    private ?AttributeValue $attributeValue = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemCategory(): ?ItemCategory
    {
        return $this->itemCategory;
    }

    public function setItemCategory(?ItemCategory $itemCategory): static
    {
        $this->itemCategory = $itemCategory;
        return $this;
    }

    public function getAttributeValue(): ?AttributeValue
    {
        return $this->attributeValue;
    }

    public function setAttributeValue(?AttributeValue $attributeValue): static
    {
        $this->attributeValue = $attributeValue;
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
}
