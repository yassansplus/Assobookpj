<?php

namespace App\Entity;

use App\Repository\ThemeAssocRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThemeAssocRepository::class)
 */
class ThemeAssoc
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Association::class, mappedBy="theme")
     */
    private $associations;

    public function __construct()
    {
        $this->associations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Association[]
     */
    public function getAssociations(): Collection
    {
        return $this->associations;
    }

    public function addAssociation(Association $association): self
    {
        if (!$this->associations->contains($association)) {
            $this->associations[] = $association;
            $association->setTheme($this);
        }

        return $this;
    }

    public function removeAssociation(Association $association): self
    {
        if ($this->associations->removeElement($association)) {
            // set the owning side to null (unless already changed)
            if ($association->getTheme() === $this) {
                $association->setTheme(null);
            }
        }

        return $this;
    }
}
