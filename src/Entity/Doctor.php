<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
class Doctor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'doctor', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $user_profile;

    #[ORM\ManyToOne(targetEntity: Hospital::class, inversedBy: 'doctors')]
    #[ORM\JoinColumn(nullable: false)]
    private $hospital;

    #[ORM\Column(type: 'string', length: 255)]
    private $specialization;

    #[ORM\Column(type: 'string', length: 255)]
    private $post;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Custom::class)]
    private $customs;

    public function __construct()
    {
        $this->customs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserProfile(): ?User
    {
        return $this->user_profile;
    }

    public function setUserProfile(User $user_profile): self
    {
        $this->user_profile = $user_profile;

        return $this;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): self
    {
        $this->specialization = $specialization;

        return $this;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function setPost(string $post): self
    {
        $this->post = $post;

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
            $custom->setDoctor($this);
        }

        return $this;
    }

    public function removeCustom(Custom $custom): self
    {
        if ($this->customs->removeElement($custom)) {
            // set the owning side to null (unless already changed)
            if ($custom->getDoctor() === $this) {
                $custom->setDoctor(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        $user=$this->getUserProfile();
        return $user->getName()." ".$user->getSurname()." ".$user->getPatronymic();
    }
}
