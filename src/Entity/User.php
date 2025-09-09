<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte associé à cette adresse mail.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 50)]
    private ?string $phone_number = null;

    #[ORM\Column(length: 255)]
    private ?string $postal_adress = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $birth_date = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $photo = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\ManyToOne(targetEntity: Car::class)]
    private ?Car $current_car = null;

    /**
     * @var Collection<int, UserCarpooling>
     */
    #[ORM\ManyToMany(targetEntity: UserCarpooling::class, mappedBy: 'user')]
    private Collection $userCarpoolings;

    /**
     * @var Collection<int, Carpooling>
     */
    #[ORM\OneToMany(targetEntity: Carpooling::class, mappedBy: 'created_by')]
    private Collection $carpoolings;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $grade = null;


    #[ORM\Column(options: ['default' => 20])]
    private int $ecopiece = 20;

    public function __construct()
    {
        $this->userCarpoolings = new ArrayCollection();
        $this->carpoolings = new ArrayCollection();
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
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {

        $roles = $this->roles;
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
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getPostalAdress(): ?string
    {
        return $this->postal_adress;
    }

    public function setPostalAdress(string $postal_adress): static
    {
        $this->postal_adress = $postal_adress;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birth_date;
    }

    public function setBirthDate(?\DateTimeImmutable $birth_date): static
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getPhoto(): ?string
    {
        if (\is_resource($this->photo)) {
            $data = stream_get_contents($this->photo);
            $this->photo = ($data === false || $data === '') ? null : $data;
        }
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCurrentCar(): ?Car
    {
        return $this->current_car;
    }

    public function setCurrentCar(?Car $current_car): static
    {
        $this->current_car = $current_car;

        return $this;
    }

    /**
     * @return Collection<int, UserCarpooling>
     */
    public function getUserCarpoolings(): Collection
    {
        return $this->userCarpoolings;
    }

    public function addUserCarpooling(UserCarpooling $userCarpooling): static
    {
        if (!$this->userCarpoolings->contains($userCarpooling)) {
            $this->userCarpoolings->add($userCarpooling);
            $userCarpooling->addUser($this);
        }

        return $this;
    }

    public function removeUserCarpooling(UserCarpooling $userCarpooling): static
    {
        if ($this->userCarpoolings->removeElement($userCarpooling)) {
            $userCarpooling->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Carpooling>
     */
    public function getCarpoolings(): Collection
    {
        return $this->carpoolings;
    }

    public function addCarpooling(Carpooling $carpooling): static
    {
        if (!$this->carpoolings->contains($carpooling)) {
            $this->carpoolings->add($carpooling);
            $carpooling->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCarpooling(Carpooling $carpooling): static
    {
        if ($this->carpoolings->removeElement($carpooling)) {
            // set the owning side to null (unless already changed)
            if ($carpooling->getCreatedBy() === $this) {
                $carpooling->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getGrade(): ?int
    {
        return $this->grade;
    }

    public function setGrade(?int $grade): static
    {
        $this->grade = $grade;

        return $this;
    }

    public function getEcopiece(): int
    {
        return $this->ecopiece;
    }

    public function setEcopiece(int $ecopiece): static
    {
        $this->ecopiece = $ecopiece;

        return $this;
    }
}
