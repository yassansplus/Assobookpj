<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="tags")
     */
    private $user_tag;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $tag;

    public function __construct()
    {
        $this->user_tag = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserTag(): Collection
    {
        return $this->user_tag;
    }

    public function addUserTag(User $userTag): self
    {
        if (!$this->user_tag->contains($userTag)) {
            $this->user_tag[] = $userTag;
        }

        return $this;
    }

    public function removeUserTag(User $userTag): self
    {
        if ($this->user_tag->contains($userTag)) {
            $this->user_tag->removeElement($userTag);
        }

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
