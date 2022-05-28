<?php

namespace App\Entity;

use App\Repository\MedicineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicineRepository::class)]
class Medicine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $price;

    #[ORM\Column(type: 'string', length: 255)]
    private $compound;

    #[ORM\Column(type: 'string', length: 255)]
    private $pharmgroup;

    #[ORM\Column(type: 'string', length: 255)]
    private $action;

    #[ORM\OneToMany(mappedBy: 'medicine', targetEntity: Provides::class, orphanRemoval: true)]
    private $providers;

    #[ORM\OneToMany(mappedBy: 'medicine', targetEntity: Contains::class, orphanRemoval: true)]
    private $customs;

    public function __construct()
    {
        $this->providers = new ArrayCollection();
        $this->customs = new ArrayCollection();
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCompound(): ?string
    {
        return $this->compound;
    }

    public function setCompound(string $compound): self
    {
        $this->compound = $compound;

        return $this;
    }

    public function getPharmgroup(): ?string
    {
        return $this->pharmgroup;
    }

    public function setPharmgroup(string $pharmgroup): self
    {
        $this->pharmgroup = $pharmgroup;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return Collection<int, Provides>
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(Provides $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers[] = $provider;
            $provider->setMedicine($this);
        }

        return $this;
    }

    public function removeProvider(Provides $provider): self
    {
        if ($this->providers->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getMedicine() === $this) {
                $provider->setMedicine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contains>
     */
    public function getCustoms(): Collection
    {
        return $this->customs;
    }

    public function addCustom(Contains $custom): self
    {
        if (!$this->customs->contains($custom)) {
            $this->customs[] = $custom;
            $custom->setMedicine($this);
        }

        return $this;
    }

    public function removeCustom(Contains $custom): self
    {
        if ($this->customs->removeElement($custom)) {
            // set the owning side to null (unless already changed)
            if ($custom->getMedicine() === $this) {
                $custom->setMedicine(null);
            }
        }

        return $this;
    }
}
