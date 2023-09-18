<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/api/user/profile', name: 'api_user_profile')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        $userWatchedMovies = $user->getWatchedMovies();
        $userWatchedSeries = $user->getSeries();
        $lastLikedMovie = $userWatchedMovies->last();
        $lastLikedSerie = $userWatchedSeries->last();
        $numberOfLikedMovies = count($userWatchedMovies);
        $numberOfLikedSeries = count($userWatchedSeries);
        $friends = $user->getFriends();
        $numberOfFriends = count($friends);

        $allMatches = [];
        foreach ($friends as $friend) {
            $matchedMovies = $user->getMatchedMoviesWith($friend);
            $matchedSeries = $user->getMatchedSeriesWith($friend);

            foreach ($matchedMovies as $movie) {
                $allMatches[] = ['type' => 'movie', 'data' => $movie, 'friend' => $friend];
            }

            foreach ($matchedSeries as $serie) {
                $allMatches[] = ['type' => 'serie', 'data' => $serie, 'friend' => $friend];
            }
        }

        if (count($allMatches) >= 4) {
            $randomKeys = array_rand($allMatches, 4);
            $threeRandomMatches = [
                $allMatches[$randomKeys[0]],
                $allMatches[$randomKeys[1]],
                $allMatches[$randomKeys[2]],
                $allMatches[$randomKeys[3]]
            ];
        } else {
            $threeRandomMatches = $allMatches;
        }

        return new JsonResponse([
            'user' => $user->toArray(),
            'userWatchedMovies' => $userWatchedMovies,
            'lastLikedMovie' => $lastLikedMovie,
            'lastLikedSerie' => $lastLikedSerie,
            'numberOfLikedMovies' => $numberOfLikedMovies,
            'numberOfLikedSeries' => $numberOfLikedSeries,
            'friends' => $friends,
            'numberOfFriends' => $numberOfFriends,
            'threeLastMatches' => $threeRandomMatches
        ]);
    }

    #[Route('/user/profile/{id}', name: 'app_user_profile_id', methods: ["GET"])]
    public function showProfile($id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        $areFriends = $this->areFriends($currentUser, $user);
        $requestSent = $this->requestSent($currentUser, $user);
        $userWatchedMovies = $user->getWatchedMovies();
        $userWatchedSeries = $user->getSeries();
        $lastLikedSerie = $userWatchedSeries->last();
        $lastLikedMovie = $userWatchedMovies->last();
        $numberOfLikedMovies = count($user->getWatchedMovies());
        $numberOfLikedSeries = count($user->getSeries());
        $matchedMovies = $user->getMatchedMoviesWith($currentUser);
        $matchedSeries = $user->getMatchedSeriesWith($currentUser);

        $matchesWithUser = [];
        foreach ($matchedMovies as $movie) {
            $matchesWithUser[] = ['type' => 'movie', 'data' => $movie, 'likedAt' => $movie->getLikeAt()];
        }

        foreach ($matchedSeries as $serie) {
            $matchesWithUser[] = ['type' => 'serie', 'data' => $serie, 'likedAt' => $serie->getLikedAt()];
        }

        if (count($matchesWithUser) >= 4) {
            $randomKeys = array_rand($matchesWithUser, 4);
            $threeRandomMatches = [
                $matchesWithUser[$randomKeys[0]],
                $matchesWithUser[$randomKeys[1]],
                $matchesWithUser[$randomKeys[2]],
                $matchesWithUser[$randomKeys[3]]
            ];
        } else {
            $threeRandomMatches = $matchesWithUser;
        }

        $friends = $user->getFriends();
        $numberOfFriends = count($user->getFriends());

        return new JsonResponse([
            'user' => $user,
            'areFriends' => $areFriends,
            'requestSent' => $requestSent,
            'numberOfFriends' => $numberOfFriends,
            'lastLikedMovie' => $lastLikedMovie,
            'lastLikedSerie' => $lastLikedSerie,
            'numberOfLikedMovies' => $numberOfLikedMovies,
            'numberOfLikedSeries' => $numberOfLikedSeries,
            'friends' => $friends,
            'matchesWithUser' => $threeRandomMatches,
        ]);
    }

    #[Route('/user/match/{id}', name: 'app_user_match')]
    public function match($id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas.');
        }

        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        $matchedMovies = $user->getMatchedMoviesWith($currentUser);
        $matchedSeries = $user->getMatchedSeriesWith($currentUser);

        return new JsonResponse([
            'user' => $user,
            'matchedMovies' => $matchedMovies,
            'matchedSeries' => $matchedSeries
        ]);
    }

    private function areFriends(User $user1, User $user2): bool
    {
        return $user1->getFriends()->contains($user2) || $user2->getFriends()->contains($user1);
    }

    private function requestSent(User $sender, User $receiver): bool
    {
        foreach ($sender->getSentFriendRequests() as $request) {
            if ($request->getReceiver() === $receiver) {
                return true;
            }
        }

        foreach ($receiver->getReceivedFriendRequests() as $request) {
            if ($request->getSender() === $sender) {
                return true;
            }
        }

        return false;
    }
}
