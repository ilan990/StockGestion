<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Put;
use App\Repository\StockMovementRepository;
use App\Trait\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\HasLifecycleCallbacks]
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
            validationContext: ['groups' => ['stockMovement:create']]
        ),
        new Put(
            security: "is_granted('EDIT', object)",
            validationContext: ['groups' => ['stockMovement:update']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    normalizationContext: ['groups' => ['stockMovement:read']],
    denormalizationContext: ['groups' => ['stockMovement:write']]
)]
#[ORM\Entity(repositoryClass: StockMovementRepository::class)]
#[ApiResource]
class StockMovement
{
    use TimestampableTrait;

    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Uuid|\Symfony\Component\Uid\UuidV7|null
     */
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['stockMovement:read'])]
    private ?Uuid $uuid = null;

    /**
     * @var int|null
     */
    #[ORM\Column]
    #[Groups(['stockMovement:read', 'stockMovement:write'])]
    private ?int $quantity = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 30)]
    #[Assert\NotNull]
    #[Assert\Choice(choices: ['in', 'out', 'adjustment'])]
    #[Groups(['stockMovement:read', 'stockMovement:write'])]
    private ?string $type = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotNull]
    #[Groups(['stockMovement:read', 'stockMovement:write'])]
    private ?string $reason = null;

    /**
     * @var \DateTimeInterface|\DateTime|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['stockMovement:read', 'stockMovement:write'])]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Bottle|null
     */
    #[ORM\ManyToOne(inversedBy: 'stockMovements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stockMovement:read', 'stockMovement:write'])]
    private ?Bottle $bottle = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(inversedBy: 'stockMovements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stockMovement:read', 'stockMovement:write'])]
    private ?User $user = null;

    /**
     * @var Organization|null
     */
    #[ORM\ManyToOne(inversedBy: 'stockMovements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stockMovement:read', 'stockMovement:write'])]
    private ?Organization $organization = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7();
        $this->date = new \DateTime();
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
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     * @return $this
     */
    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     * @return $this
     */
    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Bottle|null
     */
    public function getBottle(): ?Bottle
    {
        return $this->bottle;
    }

    /**
     * @param Bottle|null $bottle
     * @return $this
     */
    public function setBottle(?Bottle $bottle): static
    {
        $this->bottle = $bottle;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Organization|null
     */
    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    /**
     * @param Organization|null $organization
     * @return $this
     */
    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }
}
