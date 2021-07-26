<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publication
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Association::class, inversedBy="publications")
     */
    private $association;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="publication")
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity=Survey::class, inversedBy="survey", cascade={"persist", "remove"})
     */
    private $survey;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePublication;

    /**
     * @ORM\OneToOne(targetEntity=Event::class, mappedBy="publication", cascade={"persist", "remove"})
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="publication_id", orphanRemoval=true)
     */
    private $commentaires;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPublication($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPublication() === $this) {
                $comment->setPublication(null);
            }
        }

        return $this;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(?Survey $survey): self
    {
        $this->survey = $survey;

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

    public function getDatePublication():\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(): self
    {
        $this->datePublication = new DateTime();
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        // set the owning side of the relation if necessary
        if ($event->getPublication() !== $this) {
            $event->setPublication($this);
        }

        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setPublicationId($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getPublicationId() === $this) {
                $commentaire->setPublicationId(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->getDescription();
    }
}
