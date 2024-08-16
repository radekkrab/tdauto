<?php

namespace App\Entity;

use App\Repository\RequestEntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=RequestEntityRepository::class)
 * @ORM\Table(name="requests")
 */
#[ORM\Entity(repositoryClass: RequestEntityRepository::class)]
class RequestEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Car::class)]
    #[ORM\JoinColumn(name: 'carId', referencedColumnName: 'id', nullable: false)]
    private ?Car $car = null;

    #[ORM\ManyToOne(targetEntity: Program::class)]
    #[ORM\JoinColumn(name: 'programId', referencedColumnName: 'id', nullable: false)]
    private ?Program $program = null;

    #[ORM\Column(type: 'integer')]
    private int $initialPayment;

    #[ORM\Column(type: 'integer')]
    private int $loanTerm;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): static
    {
        $this->program = $program;

        return $this;
    }

    public function getInitialPayment(): int
    {
        return $this->initialPayment;
    }

    public function setInitialPayment(int $initialPayment): static
    {
        $this->initialPayment = $initialPayment;

        return $this;
    }

    public function getLoanTerm(): int
    {
        return $this->loanTerm;
    }

    public function setLoanTerm(int $loanTerm): static
    {
        $this->loanTerm = $loanTerm;

        return $this;
    }
}
