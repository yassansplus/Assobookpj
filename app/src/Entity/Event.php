<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $endingDate;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, inversedBy="event", cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotNull
     */
    private $title;

    /**
     * @ORM\OneToOne(targetEntity=Publication::class, inversedBy="event", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $publication;

    /**
     * @ORM\ManyToOne(targetEntity=Association::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $association;

    /**
     * @ORM\OneToMany(targetEntity=EventAdherent::class, mappedBy="event", orphanRemoval=true)
     */
    private $eventAdherents;

    public function __construct()
    {
        $this->eventAdherents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndingDate(): ?\DateTimeInterface
    {
        return $this->endingDate;
    }

    public function setEndingDate(\DateTimeInterface $endingDate): self
    {
        $this->endingDate = $endingDate;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(Publication $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(Association $association): self
    {
        $this->association = $association;

        return $this;
    }

    /**
     * @return Collection|EventAdherent[]
     */
    public function getEventAdherents(): Collection
    {
        return $this->eventAdherents;
    }

    public function addEventAdherent(EventAdherent $eventAdherent): self
    {
        if (!$this->eventAdherents->contains($eventAdherent)) {
            $this->eventAdherents[] = $eventAdherent;
            $eventAdherent->setEvent($this);
        }

        return $this;
    }

    public function removeEventAdherent(EventAdherent $eventAdherent): self
    {
        if ($this->eventAdherents->removeElement($eventAdherent)) {
            // set the owning side to null (unless already changed)
            if ($eventAdherent->getEvent() === $this) {
                $eventAdherent->setEvent(null);
            }
        }

        return $this;
    }

}
