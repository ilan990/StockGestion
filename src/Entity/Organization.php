<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Trait\TimestampableTrait;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
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
            validationContext: ['groups' => ['organization:create']]
        ),
        new Put(
            security: "is_granted('EDIT', object)",
            validationContext: ['groups' => ['organization:update']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    normalizationContext: ['groups' => ['organization:read']],
    denormalizationContext: ['groups' => ['organization:write']]
)]
class Organization
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
     * @var string|null
     */
    #[ORM\Column(length: 50)]
    #[Groups(['organization:read', 'organization:write'])]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 2, max: 50,
        minMessage: "Le nom doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom doit faire au maximum {{ limit }} caractères"
    )]
    private ?string $name = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Groups(['organization:read', 'organization:write'])]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    private ?string $adress = null;

    /**
     * @var Uuid|\Symfony\Component\Uid\UuidV7|null
     */
    #[Groups(['organization:read'])]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20)]
    #[Groups(['organization:read', 'organization:write'])]
    #[Assert\NotBlank(message: "Le téléphone est obligatoire")]
    #[Assert\Length(max: 20)]
    private ?string $phone = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Groups(['organization:read', 'organization:write'])]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    private ?string $email = null;

    /**
     * @var Collection|ArrayCollection
     */
    #[Groups(['organization:read'])]
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'organization')]
    private Collection $users;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'organization')]
    private Collection $categories;

    /**
     * @var Collection<int, StockMovement>
     */
    #[ORM\OneToMany(targetEntity: StockMovement::class, mappedBy: 'organization')]
    private Collection $stockMovements;

    /**
     * @var Collection<int, Bottle>
     */
    #[ORM\OneToMany(targetEntity: Bottle::class, mappedBy: 'organization')]
    private Collection $bottles;


    public function __construct()
    {
        $this->uuid = Uuid::v7();
        $this->users = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->stockMovements = new ArrayCollection();
        $this->bottles = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getAdress(): ?string
    {
        return $this->adress;
    }

    /**
     * @param string $adress
     * @return $this
     */
    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setUsers($this);
        }

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUsers() === $this) {
                $user->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setOrganization($this);
        }

        return $this;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getOrganization() === $this) {
                $category->setOrganization(null);
            }
        }

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
            $stockMovement->setOrganization($this);
        }

        return $this;
    }

    public function removeStockMovement(StockMovement $stockMovement): static
    {
        if ($this->stockMovements->removeElement($stockMovement)) {
            // set the owning side to null (unless already changed)
            if ($stockMovement->getOrganization() === $this) {
                $stockMovement->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bottle>
     */
    public function getBottles(): Collection
    {
        return $this->bottles;
    }

    public function addBottle(Bottle $bottle): static
    {
        if (!$this->bottles->contains($bottle)) {
            $this->bottles->add($bottle);
            $bottle->setOrganization($this);
        }

        return $this;
    }

    public function removeBottle(Bottle $bottle): static
    {
        if ($this->bottles->removeElement($bottle)) {
            // set the owning side to null (unless already changed)
            if ($bottle->getOrganization() === $this) {
                $bottle->setOrganization(null);
            }
        }

        return $this;
    }
}
