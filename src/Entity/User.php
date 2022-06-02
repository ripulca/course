<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @Assert\Email
     */
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $surname;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $patronymic;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $city;

    /**
     * @Assert\Regex("/^^[+]7[0-9]{10}$$/")
     */
    #[ORM\Column(type: 'string', length: 13)]
    private $phone;

    #[ORM\OneToOne(mappedBy: 'user_profile', targetEntity: Doctor::class, cascade: ['persist', 'remove'])]
    private $doctor;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Custom::class, orphanRemoval: true)]
    private $customs;

    #[ORM\OneToMany(mappedBy: 'courier', targetEntity: Custom::class)]
    private $customs_to_deliver;

    public function __construct()
    {
        $this->customs = new ArrayCollection();
        $this->customs_to_deliver = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(?string $patronymic): self
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(Doctor $doctor): self
    {
        // set the owning side of the relation if necessary
        if ($doctor->getUserProfile() !== $this) {
            $doctor->setUserProfile($this);
        }

        $this->doctor = $doctor;

        return $this;
    }

    /**
     * @return Collection<int, Custom>
     */
    public function getCustomsToDeliver(): Collection
    {
        return $this->customs_to_deliver;
    }

    public function addCustomToDeliver(Custom $custom_to_deliver): self
    {
        if (!$this->customs_to_deliver->contains($custom_to_deliver)) {
            $this->customs_to_deliver[] = $custom_to_deliver;
            $custom_to_deliver->setCourier($this);
        }

        return $this;
    }

    public function removeCustomToDeliver(Custom $custom_to_deliver): self
    {
        if ($this->customs_to_deliver->removeElement($custom_to_deliver)) {
            // set the owning side to null (unless already changed)
            if ($custom_to_deliver->getCourier() === $this) {
                $custom_to_deliver->setCourier(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Custom>
     */
    public function getCustoms(): Collection
    {
        return $this->customs;
    }

    public function addCustom(Custom $custom): self
    {
        if (!$this->customs->contains($custom)) {
            $this->customs[] = $custom;
            $custom->setCustomer($this);
        }

        return $this;
    }

    public function removeCustom(Custom $custom): self
    {
        if ($this->customs->removeElement($custom)) {
            // set the owning side to null (unless already changed)
            if ($custom->getCustomer() === $this) {
                $custom->setCustomer(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName()." ".$this->getSurname()." ".$this->getPatronymic();
    }
}
