<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 13)]
    private $phone;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Provides::class, orphanRemoval: true)]
    private $provides;

    public function __construct()
    {
        $this->provides = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
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
     * @return Collection<int, Provides>
     */
    public function getProvides(): Collection
    {
        return $this->provides;
    }

    public function addProvide(Provides $provide): self
    {
        if (!$this->provides->contains($provide)) {
            $this->provides[] = $provide;
            $provide->setProvider($this);
        }

        return $this;
    }

    public function removeProvide(Provides $provide): self
    {
        if ($this->provides->removeElement($provide)) {
            // set the owning side to null (unless already changed)
            if ($provide->getProvider() === $this) {
                $provide->setProvider(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string)$this->getName();
    }
}
