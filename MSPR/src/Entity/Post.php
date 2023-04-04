<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_held = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_post = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'guarded_posts')]
    private ?User $guardian = null;

    #[ORM\Column(type: "string")]
    private $image = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plant $plantId = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function IsHeld(): ?bool
    {
        return $this->is_held;
    }

    public function setIsHeld(?bool $is_held): self
    {
        $this->is_held = $is_held;

        return $this;
    }
    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getUserPost(): ?User
    {
        return $this->user_post;
    }

    public function setUserPost(?User $user_post): self
    {
        $this->user_post = $user_post;

        return $this;
    }

    public function getGuardian(): ?User
    {
        return $this->guardian;
    }

    public function setGuardian(?User $guardian): self
    {
        $this->guardian = $guardian;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPlantId(): ?Plant
    {
        return $this->plantId;
    }

    public function setPlantId(?Plant $plantId): self
    {
        $this->plantId = $plantId;

        return $this;
    }
}
