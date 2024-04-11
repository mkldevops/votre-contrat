<?php

namespace App\Entity;

use App\Entity\Enum\TemplateEnum;
use App\Entity\Trait\IdEntityTrait;
use App\Entity\Trait\TimestampableEntityTrait;
use App\Repository\FormationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation implements EntityInterface
{
    use IdEntityTrait;
    use TimestampableEntityTrait;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(inversedBy: 'formations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column]
    private ?int $duration = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private TemplateEnum $template = TemplateEnum::basic;

    #[Override]
    public function __toString(): string
    {
        return sprintf('[ %s ] - %s', $this->company?->getName(), $this->name);
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getTemplate(): TemplateEnum
    {
        return $this->template;
    }

    public function setTemplate(TemplateEnum $template): static
    {
        $this->template = $template;

        return $this;
    }
}
