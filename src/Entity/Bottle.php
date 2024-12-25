<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\BottleRepository;
use App\Security\Voter\BottleVoter;
use App\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BottleRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            security: "is_granted('VIEW', object)"
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            validationContext: ['groups' => ['bottle:create']]
        ),
        new Put(
            security: "is_granted('EDIT', object)",
            validationContext: ['groups' => ['bottle:update']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
)]
class Bottle
{
    use TimestampableTrait;

    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['bottle:read'])]
    private ?int $id = null;

    /**
     * @var UuidV7|Uuid|null
     */
    #[Groups(['category:read'])]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50)]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $name = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 50)]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $type = null;

    /**
     * @var int|null
     */
    #[ORM\Column]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\PositiveOrZero]
    private ?int $quantity = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\Positive]
    private ?string $buyingPrice = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\Positive]
    private ?string $sellingPrice = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['bottle:read', 'bottle:write'])]
    private ?string $supplier = null;

    /**
     * @var int|null
     */
    #[ORM\Column]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\PositiveOrZero]
    private ?int $minQuantity = null;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\Positive]
    private ?int $volume = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\Range(min: 0, max: 100)]
    private ?string $alcoholDegree = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['bottle:read', 'bottle:write'])]
    private ?string $reference = null;

    /**
     * @var Category|null
     */
    #[ORM\ManyToOne(inversedBy: 'bottles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['bottle:read', 'bottle:write'])]
    #[Assert\NotNull]
    private ?Category $category = null;

    /**
     * @var Collection<int, StockMovement>
     */
    #[ORM\OneToMany(targetEntity: StockMovement::class, mappedBy: 'bottle')]
    private Collection $stockMovements;

    #[ORM\ManyToOne(inversedBy: 'bottles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;


    public function __construct()
    {
        $this->uuid = Uuid::v7();
        $this->stockMovements = new ArrayCollection();

    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Uuid|null
     */
    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    /**
     * @param Uuid $uuid
     * @return $this
     */
    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBuyingPrice(): ?string
    {
        return $this->buyingPrice;
    }

    /**
     * @param string $buyingPrice
     * @return $this
     */
    public function setBuyingPrice(string $buyingPrice): static
    {
        $this->buyingPrice = $buyingPrice;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSellingPrice(): ?string
    {
        return $this->sellingPrice;
    }

    /**
     * @param string $sellingPrice
     * @return $this
     */
    public function setSellingPrice(string $sellingPrice): static
    {
        $this->sellingPrice = $sellingPrice;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSupplier(): ?string
    {
        return $this->supplier;
    }

    /**
     * @param string|null $supplier
     * @return $this
     */
    public function setSupplier(?string $supplier): static
    {
        $this->supplier = $supplier;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinQuantity(): ?int
    {
        return $this->minQuantity;
    }

    /**
     * @param int $minQuantity
     * @return $this
     */
    public function setMinQuantity(int $minQuantity): static
    {
        $this->minQuantity = $minQuantity;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getVolume(): ?int
    {
        return $this->volume;
    }

    /**
     * @param int|null $volume
     * @return $this
     */
    public function setVolume(?int $volume): static
    {
        $this->volume = $volume;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlcoholDegree(): ?string
    {
        return $this->alcoholDegree;
    }

    /**
     * @param string|null $alcoholDegree
     * @return $this
     */
    public function setAlcoholDegree(?string $alcoholDegree): static
    {
        $this->alcoholDegree = $alcoholDegree;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     * @return $this
     */
    public function setReference(?string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name . ' (' . $this->type . ')';
    }

    // Méthodes métier précédentes

    /**
     * @return float|null
     */
    public function getMargin(): ?float
    {
        if ($this->sellingPrice === null || $this->buyingPrice === null) {
            return null;
        }
        return floatval($this->sellingPrice) - floatval($this->buyingPrice);
    }

    /**
     * @return float|null
     */
    public function getMarginPercentage(): ?float
    {
        if ($this->sellingPrice === null || $this->buyingPrice === null || floatval($this->sellingPrice) == 0) {
            return null;
        }
        $margin = $this->getMargin();
        return $margin !== null ? ($margin / floatval($this->sellingPrice)) * 100 : null;
    }

    /**
     * @return bool
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minQuantity;
    }

    /**
     * @return Collection<int, StockMovement>
     */
    public function getStockMovements(): Collection
    {
        return $this->stockMovements;
    }

    public function addStockMovement(StockMovement $stockMovement): static
    {
        if (!$this->stockMovements->contains($stockMovement)) {
            $this->stockMovements->add($stockMovement);
            $stockMovement->setBottle($this);
        }

        return $this;
    }

    public function removeStockMovement(StockMovement $stockMovement): static
    {
        if ($this->stockMovements->removeElement($stockMovement)) {
            // set the owning side to null (unless already changed)
            if ($stockMovement->getBottle() === $this) {
                $stockMovement->setBottle(null);
            }
        }

        return $this;
    }
}