<?php

namespace App\Entity;


use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Trait\TimestampableTrait;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
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
            validationContext: ['groups' => ['user:create']]
        ),
        new Put(
            security: "is_granted('EDIT', object)",
            validationContext: ['groups' => ['user:update']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    // Contrôle quels champs sont visibles lors de la lecture
    normalizationContext: ['groups' => ['user:read']],
    // Contrôle quels champs sont modifiables lors de l'écriture
    denormalizationContext: ['groups' => ['user:write']]
)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_MANAGER = 'ROLE_MANAGER';
    public const ROLE_BARMAN = 'ROLE_BARMAN';

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user:read', 'user:write'])]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas un email valide.")]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read'])]
    #[Assert\Choice(choices: [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_BARMAN],
        message: "Le rôle '{{ value }}' n'est pas valide.")]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[Groups(['user:write'])]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères')]
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 60, nullable: true)]
    #[Assert\Length(min: 2, max: 60, minMessage: "Le prénom doit faire au moins {{ limit }} caractères", maxMessage: "Le prénom doit faire max {{ limit }} caractères")]
    private ?string $firstName = null;

    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min: 2, max: 60, minMessage: "Le nom doit faire au moins {{ limit }} caractères", maxMessage: "Le nom doit faire max {{ limit }} caractères")]
    private ?string $lastName = null;

    #[Groups(['user:read'])]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read', 'user:write'])]
    private ?Organization $organization = null;

    /**
     * @var Collection<int, StockMovement>
     */
    #[ORM\OneToMany(targetEntity: StockMovement::class, mappedBy: 'user')]
    private Collection $stockMovements;

    public function __construct()
    {
        $this->uuid = Uuid::v7();
        $this->stockMovements = new ArrayCollection();
    }


    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
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
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
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
            $stockMovement->setUser($this);
        }

        return $this;
    }

    public function removeStockMovement(StockMovement $stockMovement): static
    {
        if ($this->stockMovements->removeElement($stockMovement)) {
            // set the owning side to null (unless already changed)
            if ($stockMovement->getUser() === $this) {
                $stockMovement->setUser(null);
            }
        }

        return $this;
    }
}
