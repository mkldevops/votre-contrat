<?php

namespace App\Entity;

use App\Entity\Trait\IdEntityTrait;
use App\Entity\Trait\TimestampableEntityTrait;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company implements EntityInterface
{
    use IdEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Formation::class, orphanRemoval: true)]
    private Collection $formations;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 6)]
    private ?string $postcode = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $activityNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $representative = null;

    #[ORM\Column(length: 100)]
    private ?string $rcs = null;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setCompany($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): static
    {
        // set the owning side to null (unless already changed)
        if ($this->formations->removeElement($formation) && $formation->getCompany() === $this) {
            $formation->setCompany(null);
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): static
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getActivityNumber(): ?string
    {
        return $this->activityNumber;
    }

    public function setActivityNumber(string $activityNumber): static
    {
        $this->activityNumber = $activityNumber;

        return $this;
    }

    public function getRepresentative(): ?string
    {
        return $this->representative;
    }

    public function setRepresentative(string $representative): static
    {
        $this->representative = $representative;

        return $this;
    }

    public function getRcs(): ?string
    {
        return $this->rcs;
    }

    public function setRcs(string $rcs): static
    {
        $this->rcs = $rcs;

        return $this;
    }
}
