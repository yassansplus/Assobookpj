<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AssociationRepository::class)
 */
class Association
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull(message="Le nom de l'association ne peut pas être vide")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull(message="La description de l'association ne peut pas être vide")
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="association", cascade={"persist", "remove"})
     */
    private $user_account;

    /**
     * @ORM\OneToMany(targetEntity=Publication::class, mappedBy="association")
     */
    private $publications;

    /**
     * @ORM\ManyToMany(targetEntity=Adherent::class, mappedBy="associations")
     */
    private $adherents;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, inversedBy="association", cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=ThemeAssoc::class, inversedBy="associations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     *     pattern="%^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)[a-z0-9]+([\-\.]{1})*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$%",
     *     match=true,
     *     message="Vous devez rentrer une URL"
     * )
     */
    private $website;

    /**
     * @ORM\OneToMany(targetEntity=Cagnotte::class, mappedBy="association")
     */
    private $cagnottes;

    /**
     * @ORM\Column(type="boolean",options={"default" : false})
     */
    private $haveCagnotte;


    public function __construct()
    {
        $this->publications = new ArrayCollection();
        $this->adherents = new ArrayCollection();
        $this->cagnottes = new ArrayCollection();
        $this->haveCagnotte = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
            $this->user_account->setAssociation(null);
        }

        // set the owning side of the relation if necessary
        if ($user_account !== null && $user_account->getAssociation() !== $this) {
            $user_account->setAssociation($this);
        }

        $this->user_account = $user_account;

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publications->contains($publication)) {
            $this->publications[] = $publication;
            $publication->setAssociation($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getAssociation() === $this) {
                $publication->setAssociation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Adherent[]
     */
    public function getAdherents(): Collection
    {
        return $this->adherents;
    }

    public function addAdherent(Adherent $adherent): self
    {
        $this->adherents[] = $adherent;
        $adherent->addAssociation($this);

        return $this;
    }

    public function removeAdherent(Adherent $adherent): self
    {
        if ($this->adherents->removeElement($adherent)) {
            $adherent->removeAssociation($this);
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getTheme(): ?ThemeAssoc
    {
        return $this->theme;
    }

    public function setTheme(?ThemeAssoc $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

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
            $cagnotte->setAssociation($this);
        }

        return $this;
    }

    public function removeCagnotte(Cagnotte $cagnotte): self
    {
        if ($this->cagnottes->removeElement($cagnotte)) {
            // set the owning side to null (unless already changed)
            if ($cagnotte->getAssociation() === $this) {
                $cagnotte->setAssociation(null);
            }
        }

        return $this;
    }

    public function getHaveCagnotte(): ?bool
    {
        return $this->haveCagnotte;
    }

    public function setHaveCagnotte(bool $haveCagnotte): self
    {
        $this->haveCagnotte = $haveCagnotte;

        return $this;
    }

}
