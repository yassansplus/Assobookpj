<?php

namespace App\Entity;

use App\Repository\AdherentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AdherentRepository::class)
 */
class Adherent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull(message="Votre prénom ne peut pas être vide")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull(message="Votre nom ne peut pas être vide")
     */
    private $lastname;

    /**
     * @ORM\Column(type="boolean")
     */
    private $gender;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull(message="Votre date de naissance est requise.")
     */
    private $birthday;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="adherent", cascade={"persist", "remove"})
     */
    private $user_account;

    /**
     * @ORM\ManyToMany(targetEntity=Association::class, inversedBy="adherents")
     */
    private $associations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $search;

    /**
     * @ORM\OneToMany(targetEntity=Cagnotte::class, mappedBy="donateur")
     */
    private $cagnottes;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="adherent", orphanRemoval=true)
     */
    private $conversations;

    public function __construct()
    {
        $this->associations = new ArrayCollection();
        $this->cagnottes = new ArrayCollection();
        $this->conversations = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(bool $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getUserAccount(): ?User
    {
        return $this->user_account;
    }

    public function setUserAccount(?User $user_account): self
    {
        // unset the owning side of the relation if necessary
        if ($user_account === null && $this->user_account !== null) {
            $this->user_account->setAdherent(null);
        }

        // set the owning side of the relation if necessary
        if ($user_account !== null && $user_account->getAdherent() !== $this) {
            $user_account->setAdherent($this);
        }

        $this->user_account = $user_account;

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
        }

        return $this;
    }

    public function removeAssociation(Association $association): self
    {
        $this->associations->removeElement($association);

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(string $search): self
    {
        $this->search = $search;

        return $this;
    }


    /**
     * @return Collection|Cagnotte[]
     */
    public function getCagnottes(): Collection
    {
        return $this->cagnottes;
    }

    public function addCagnotte(Cagnotte $cagnotte): self
    {
        if (!$this->cagnottes->contains($cagnotte)) {
            $this->cagnottes[] = $cagnotte;
            $cagnotte->setDonateur($this);
        }

        return $this;
    }

    public function removeCagnotte(Cagnotte $cagnotte): self
    {
        if ($this->cagnottes->removeElement($cagnotte)) {
            // set the owning side to null (unless already changed)
            if ($cagnotte->getDonateur() === $this) {
                $cagnotte->setDonateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->setAdherent($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            // set the owning side to null (unless already changed)
            if ($conversation->getAdherent() === $this) {
                $conversation->setAdherent(null);
            }
        }

        return $this;
    }

}
