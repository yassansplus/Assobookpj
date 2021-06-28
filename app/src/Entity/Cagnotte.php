<?php

namespace App\Entity;

use App\Repository\CagnotteRepository;
use Doctrine\ORM\Mapping as ORM;

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
}
