<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $pseudo = null;

    #[ORM\OneToMany(mappedBy: 'userID', targetEntity: Tweet::class, orphanRemoval: true)]
    private Collection $tweet;

    public function __construct()
    {
        $this->tweet = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, Tweet>
     */
    public function getTweet(): Collection
    {
        return $this->tweet;
    }

    public function addTweet(Tweet $tweet): self
    {
        if (!$this->tweet->contains($tweet)) {
            $this->tweet->add($tweet);
            $tweet->setUserID($this);
        }

        return $this;
    }

    public function removeTweet(Tweet $tweet): self
    {
        if ($this->tweet->removeElement($tweet)) {
            // set the owning side to null (unless already changed)
            if ($tweet->getUserID() === $this) {
                $tweet->setUserID(null);
            }
        }

        return $this;
    }
}
