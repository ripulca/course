<?php

namespace App\Entity;

use App\Repository\ProvidesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProvidesRepository::class)]
class Provides
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'provides')]
    #[ORM\JoinColumn(nullable: false)]
    private $provider;

    #[ORM\ManyToOne(targetEntity: Medicine::class, inversedBy: 'providers')]
    #[ORM\JoinColumn(nullable: false)]
    private $medicine;

    #[ORM\Column(type: 'integer')]
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getMedicine(): ?Medicine
    {
        return $this->medicine;
    }

    public function setMedicine(?Medicine $medicine): self
    {
        $this->medicine = $medicine;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
