<?php

namespace App\Entity;

use App\Repository\CustomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomRepository::class)]
class Custom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'customs')]
    #[ORM\JoinColumn(nullable: false)]
    private $customer;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'customs_to_deliver')]
    private $courier;

    #[ORM\ManyToOne(targetEntity: Doctor::class, inversedBy: 'customs')]
    private $doctor;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $price;

    #[ORM\Column(type: 'date')]
    private $payment_date;

    #[ORM\Column(type: 'date')]
    private $complete_date;

    #[ORM\OneToMany(mappedBy: 'custom', targetEntity: Contains::class, orphanRemoval: true)]
    private $contains;

    #[ORM\Column(type: 'boolean')]
    private $is_in_cart;

    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    public function __construct()
    {
        $this->contains = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCourier(): ?User
    {
        return $this->courier;
    }

    public function setCourier(?User $courier): self
    {
        $this->courier = $courier;

        return $this;
    }

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): self
    {
        $this->doctor = $doctor;

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

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->payment_date;
    }

    public function setPaymentDate(\DateTimeInterface $payment_date): self
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    public function getCompleteDate(): ?\DateTimeInterface
    {
        return $this->complete_date;
    }

    public function setCompleteDate(\DateTimeInterface $complete_date): self
    {
        $this->complete_date = $complete_date;

        return $this;
    }

    /**
     * @return Collection<int, Contains>
     */
    public function getContains(): Collection
    {
        return $this->contains;
    }

    public function addContain(Contains $contain): self
    {
        if (!$this->contains->contains($contain)) {
            $this->contains[] = $contain;
            $contain->setCustom($this);
        }

        return $this;
    }

    public function removeContain(Contains $contain): self
    {
        if ($this->contains->removeElement($contain)) {
            // set the owning side to null (unless already changed)
            if ($contain->getCustom() === $this) {
                $contain->setCustom(null);
            }
        }

        return $this;
    }

    public function isIsInCart(): ?bool
    {
        return $this->is_in_cart;
    }

    public function setIsInCart(bool $is_in_cart): self
    {
        $this->is_in_cart = $is_in_cart;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }
}
