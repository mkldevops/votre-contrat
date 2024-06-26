<?php

namespace App\Entity;

use App\Entity\Trait\IdEntityTrait;
use App\Entity\Trait\TimestampableEntityTrait;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'There is already a company with this name')]
#[UniqueEntity(fields: ['activityNumber'], message: 'There is already a company with this activity number')]
#[UniqueEntity(fields: ['rcs'], message: 'There is already a company with this RCS')]
#[ORM\UniqueConstraint(name: 'UNIQ_ACTIVITY_NUMBER', fields: ['activityNumber'])]
#[ORM\UniqueConstraint(name: 'UNIQ_RCS', fields: ['rcs'])]
#[ORM\UniqueConstraint(name: 'UNIQ_NAME', fields: ['name'])]
class Company implements EntityInterface
{
    use IdEntityTrait;
    use TimestampableEntityTrait;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Formation::class, orphanRemoval: true)]
    private Collection $formations;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 6)]
    #[ORM\Column(length: 6)]
    private ?string $postcode = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[Assert\Email]
    #[Assert\Length(max: 180)]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$/',
        message: 'The phone number is not valid, it must be a French phone number (Ex: +33 1 23 54 78 96).'
    )]
    #[Assert\Length(min: 8, max: 20)]
    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[Assert\NotBlank]
    #[Assert\Regex('/^\d+$/', message: 'The activity number is not valid, it must be a digits number')]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50)]
    private ?string $activityNumber = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $representative = null;

    #[Assert\NotBlank]
    #[Assert\Regex('/^\d+$/', message: 'The RCS is not valid, it must be a digits number')]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50)]
    private ?string $rcs = null;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    #[Override]
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
