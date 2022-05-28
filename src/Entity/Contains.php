<?php

namespace App\Entity;

use App\Repository\ContainsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContainsRepository::class)]
class Contains
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Custom::class, inversedBy: 'contains')]
    #[ORM\JoinColumn(nullable: false)]
    private $custom;

    #[ORM\ManyToOne(targetEntity: Medicine::class, inversedBy: 'customs')]
    #[ORM\JoinColumn(nullable: false)]
    private $medicine;

    #[ORM\Column(type: 'integer')]
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustom(): ?Custom
    {
        return $this->custom;
    }

    public function setCustom(?Custom $custom): self
    {
        $this->custom = $custom;

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
