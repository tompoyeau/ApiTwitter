<?php

namespace App\Entity;

use App\Repository\TweetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TweetRepository::class)]
class Tweet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groupTweet', 'groupUser'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['groupTweet', 'groupUser'])]
    private ?string $texte = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['groupTweet', 'groupUser'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'tweet')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupTweet'])]
    private ?User $userID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUserID(): ?User
    {
        return $this->userID;
    }

    public function setUserID(?User $userID): self
    {
        $this->userID = $userID;

        return $this;
    }
}
