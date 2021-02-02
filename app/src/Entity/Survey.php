<?php

namespace App\Entity;

use App\Repository\SurveyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SurveyRepository::class)
 */
class Survey
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $title;


    /**
     * @ORM\Column(type="json")
     */
    private $options = [];

    /**
     * @ORM\OneToOne(targetEntity=Publication::class, mappedBy="survey", cascade={"persist", "remove"})
     */
    private $publication;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): self
    {
        // unset the owning side of the relation if necessary
        if ($publication === null && $this->publication !== null) {
            $this->publication->setSurvey(null);
        }

        // set the owning side of the relation if necessary
        if ($publication !== null && $publication->getSurvey() !== $this) {
            $publication->setSurvey($this);
        }

        $this->publication = $publication;

        return $this;
    }
}
