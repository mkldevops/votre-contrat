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
}
