<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Movie::class, mappedBy: 'watchedByUsers')]
    private Collection $watchedMovies;

    #[ORM\ManyToMany(targetEntity: Movie::class, mappedBy: 'dislikeByUser')]
    private Collection $dislikeMovies;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: FriendsRequest::class)]
    private Collection $sentFriendRequests;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: FriendsRequest::class)]
    private Collection $receivedFriendRequests;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Serie::class, mappedBy: 'watchedByUsers')]
    private Collection $series;

    #[ORM\ManyToMany(targetEntity: Serie::class, mappedBy: 'dislikeByUser')]
    private Collection $dislikeSeries;

    public function __construct()
    {
        $this->watchedMovies = new ArrayCollection();
        $this->dislikeMovies = new ArrayCollection();
        $this->sentFriendRequests = new ArrayCollection();
        $this->receivedFriendRequests = new ArrayCollection();
        $this->series = new ArrayCollection();
        $this->dislikeSeries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getWatchedMovies(): Collection
    {
        return $this->watchedMovies;
    }

    public function getMatchedMoviesWith(User $friend): array
    {
        $matchedMovies = [];
        foreach ($this->getWatchedMovies() as $movie) {
            if ($friend->hasLikedMovie($movie)) {
                $matchedMovies[] = $movie;
            }
        }
        return $matchedMovies;
    }

    public function hasLikedMovie(Movie $movie): bool
    {
        return $this->watchedMovies->contains($movie);
    }

    public function addWatchedMovie(Movie $watchedMovie): static
    {
        if (!$this->watchedMovies->contains($watchedMovie)) {
            $this->watchedMovies->add($watchedMovie);
            $watchedMovie->addWatchedByUser($this);
        }

        return $this;
    }

    public function removeWatchedMovie(Movie $watchedMovie): static
    {
        if ($this->watchedMovies->removeElement($watchedMovie)) {
            $watchedMovie->removeWatchedByUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getDislikeMovies(): Collection
    {
        return $this->dislikeMovies;
    }

    public function addDislikeMovie(Movie $dislikeMovie): static
    {
        if (!$this->dislikeMovies->contains($dislikeMovie)) {
            $this->dislikeMovies->add($dislikeMovie);
            $dislikeMovie->addDislikeByUser($this);
        }

        return $this;
    }

    public function removeDislikeMovie(Movie $dislikeMovie): static
    {
        if ($this->dislikeMovies->removeElement($dislikeMovie)) {
            $dislikeMovie->removeDislikeByUser($this);
        }

        return $this;
    }

    public function getFriends(): Collection
    {
        $friends = new ArrayCollection();

        foreach ($this->receivedFriendRequests as $friendRequest) {
            if ($friendRequest->isAccepted()) {
                $friends->add($friendRequest->getSender());
            }
        }

        foreach ($this->sentFriendRequests as $friendRequest) {
            if ($friendRequest->isAccepted()) {
                $friends->add($friendRequest->getReceiver());
            }
        }

        return $friends;
    }

    public function removeFriend(User $friend): ?FriendsRequest
    {
        foreach ($this->receivedFriendRequests as $friendRequest) {
            if ($friendRequest->isAccepted() && $friendRequest->getSender() === $friend) {
                $this->receivedFriendRequests->removeElement($friendRequest);
                return $friendRequest;
            }
        }

        foreach ($this->sentFriendRequests as $friendRequest) {
            if ($friendRequest->isAccepted() && $friendRequest->getReceiver() === $friend) {
                $this->sentFriendRequests->removeElement($friendRequest);
                return $friendRequest;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, FriendsRequest>
     */
    public function getSentFriendRequests(): Collection
    {
        return $this->sentFriendRequests;
    }

    public function addSentFriendRequest(FriendsRequest $sentFriendRequest): static
    {
        if (!$this->sentFriendRequests->contains($sentFriendRequest)) {
            $this->sentFriendRequests->add($sentFriendRequest);
            $sentFriendRequest->setSender($this);
        }

        return $this;
    }

    public function removeSentFriendRequest(FriendsRequest $sentFriendRequest): static
    {
        if ($this->sentFriendRequests->removeElement($sentFriendRequest)) {
            // set the owning side to null (unless already changed)
            if ($sentFriendRequest->getSender() === $this) {
                $sentFriendRequest->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendsRequest>
     */
    public function getReceivedFriendRequests(): Collection
    {
        return $this->receivedFriendRequests;
    }

    public function addReceivedFriendRequest(FriendsRequest $receivedFriendRequest): static
    {
        if (!$this->receivedFriendRequests->contains($receivedFriendRequest)) {
            $this->receivedFriendRequests->add($receivedFriendRequest);
            $receivedFriendRequest->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedFriendRequest(FriendsRequest $receivedFriendRequest): static
    {
        if ($this->receivedFriendRequests->removeElement($receivedFriendRequest)) {
            // set the owning side to null (unless already changed)
            if ($receivedFriendRequest->getReceiver() === $this) {
                $receivedFriendRequest->setReceiver(null);
            }
        }

        return $this;
    }

    public function getAcceptedFriends(): Collection
    {
        $friends = new ArrayCollection();

        foreach ($this->receivedFriendRequests as $friendRequest) {
            if ($friendRequest->isAccepted()) {
                $friends->add($friendRequest->getSender());
            }
        }

        foreach ($this->sentFriendRequests as $friendRequest) {
            if ($friendRequest->isAccepted()) {
                $friends->add($friendRequest->getReceiver());
            }
        }

        return $friends;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;

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

    /**
     * @return Collection<int, Serie>
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function getMatchedSeriesWith(User $friend): array
    {
        $matchedSeries = [];
        foreach ($this->getSeries() as $serie) {
            if ($friend->hasLikedSerie($serie)) {
                $matchedSeries[] = $serie;
            }
        }
        return $matchedSeries;
    }

    public function hasLikedSerie(Serie $serie): bool
    {
        return $this->series->contains($serie);
    }

    public function addSeries(Serie $series): static
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
            $series->addWatchedByUser($this);
        }

        return $this;
    }

    public function removeSeries(Serie $series): static
    {
        if ($this->series->removeElement($series)) {
            $series->removeWatchedByUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Serie>
     */
    public function getDislikeSeries(): Collection
    {
        return $this->dislikeSeries;
    }

    public function addDislikeSeries(Serie $dislikeSeries): static
    {
        if (!$this->dislikeSeries->contains($dislikeSeries)) {
            $this->dislikeSeries->add($dislikeSeries);
            $dislikeSeries->addDislikeByUser($this);
        }

        return $this;
    }

    public function removeDislikeSeries(Serie $dislikeSeries): static
    {
        if ($this->dislikeSeries->removeElement($dislikeSeries)) {
            $dislikeSeries->removeDislikeByUser($this);
        }

        return $this;
    }
}
