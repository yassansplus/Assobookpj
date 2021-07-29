<?php

namespace App\Entity;

use App\Repository\CagnotteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CagnotteRepository::class)
 */
class Cagnotte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotNull(message="Le montant ne peut pas être vide")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity=Adherent::class, inversedBy="cagnottes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $donateur;

    /**
     * @ORM\ManyToOne(targetEntity=Association::class, inversedBy="cagnottes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $association;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDonateur(): ?Adherent
    {
        return $this->donateur;
    }

    public function setDonateur(?Adherent $donateur): self
    {
        $this->donateur = $donateur;

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): self
    {
        $this->association = $association;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
