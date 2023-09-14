<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $movieDbId = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'watchedMovies')]
    private Collection $watchedByUsers;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'dislikeMovies')]
    #[ORM\JoinTable(name: "movie_user_dislike")]
    private Collection $dislikeByUser;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $posterPath = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trailerUrl = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $likeAt = null;


    public function __construct()
    {
        $this->watchedByUsers = new ArrayCollection();
        $this->dislikeByUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovieDbId(): ?int
    {
        return $this->movieDbId;
    }

    public function setMovieDbId(int $movieDbId): static
    {
        $this->movieDbId = $movieDbId;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getWatchedByUsers(): Collection
    {
        return $this->watchedByUsers;
    }

    public function addWatchedByUser(User $watchedByUser): static
    {
        if (!$this->watchedByUsers->contains($watchedByUser)) {
            $this->watchedByUsers->add($watchedByUser);
        }

        return $this;
    }

    public function removeWatchedByUser(User $watchedByUser): static
    {
        $this->watchedByUsers->removeElement($watchedByUser);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getDislikeByUser(): Collection
    {
        return $this->dislikeByUser;
    }

    public function addDislikeByUser(User $dislikeByUser): static
    {
        if (!$this->dislikeByUser->contains($dislikeByUser)) {
            $this->dislikeByUser->add($dislikeByUser);
        }

        return $this;
    }

    public function removeDislikeByUser(User $dislikeByUser): static
    {
        $this->dislikeByUser->removeElement($dislikeByUser);

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPosterPath(): ?string
    {
        return $this->posterPath;
    }

    public function setPosterPath(?string $posterPath): static
    {
        $this->posterPath = $posterPath;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTrailerUrl(): ?string
    {
        return $this->trailerUrl;
    }

    public function setTrailerUrl(?string $trailerUrl): static
    {
        $this->trailerUrl = $trailerUrl;

        return $this;
    }

    public function getLikeAt(): ?\DateTimeInterface
    {
        return $this->likeAt;
    }

    public function setLikeAt(?\DateTimeInterface $likeAt): static
    {
        $this->likeAt = $likeAt;

        return $this;
    }
}
