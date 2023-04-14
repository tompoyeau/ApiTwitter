<?php

namespace App\Controller;

use App\Repository\TweetRepository;
use App\Entity\Tweet;

use App\DTO\TweetDTO;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TweetController extends AbstractController
{
    #[Route('/api/tweets', name: 'tweets_list', methods: 'GET')]
    #[OA\Get(
        summary: 'Retourne la liste des tweets',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des tweets',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Tweet::class, groups: ['groups' => 'groupTweet']))
                )
            )
        ]
    )]
    #[OA\Tag(name: "Tweets")]
    public function AllTweets(TweetRepository $repository): JsonResponse
    {
        $tweets = $repository->findAll();
        return $this->json($tweets, 200, [], [
            "groups" => ["groupTweet", "group1"]
        ]);
    }

    #[Route('/api/tweet/{id}', name: 'find_tweet', methods: 'GET')]
    #[OA\Get(
        summary: 'Retourne un tweet',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Le tweet',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Tweet::class, groups: ['groups' => 'groupTweet']))
                )
            )
        ]
    )]
    #[OA\Tag(name: "Tweets")]
    public function show(EntityManagerInterface $entityManager, int $id, SerializerInterface $serializer): JsonResponse
    {
        $tweet = $entityManager->getRepository(Tweet::class)->find($id);
        if (!$tweet) {
            throw $this->createNotFoundException(
                'No tweet found for id ' . $id
            );
        }
        $jsonContent = $serializer->serialize($tweet, 'json', [
            'groups' => ['groupTweet'],
        ]);
        return $this->json($tweet, 200, [], [
            "groups" => ["groupTweet", "group1"]

        ]);
    }

    #[Route('/api/tweet/{id}', name: 'tweet_delete', methods: 'DELETE')]
    #[OA\Delete(
        summary: 'Suppression d\'un tweet',
        responses: [
            new OA\Response(
                response: 204,
                description: 'Tweet supprimé',
            )
        ]
    )]
    #[OA\Tag(name: "Tweets")]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $tweet = $entityManager->getRepository(Tweet::class)->find($id);
        if (!$tweet) {
            throw $this->createNotFoundException(
                'No tweet found for id ' . $id
            );
        }
        $entityManager->remove($tweet);
        $entityManager->flush();
        return $this->json(null, 204);
    }

    #[Route('/api/tweet/create', name: 'create_tweet', methods: 'POST')]
    #[OA\Post(
        summary: 'Création d\'un tweet',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Informations d\'un tweet',
            content: new OA\JsonContent(
                ref: new Model(type: TweetDTO::class)
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Identifiant du tweet'
            ),
        ]
    )]
    #[OA\Tag(name: "Tweets")]
    public function createTweet(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        // On récupère les données envoyées dans la requête
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json($errorMessages, 400);
        }

        $user = $entityManager->getRepository(User::class)->find($data['userID']);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id ' . $data['userID']
            );
        }

        $tweet = new Tweet();
        $tweet->setTexte($data['texte']);
        $tweet->setUserID($data['userID']);
        $tweet->setDate(new DateTime());

        try {
            $entityManager->persist($tweet);
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue lors de la création du tweet.'], 500);
        }
        return $this->json($tweet, 201, [], [
            'groups' => ['groupTweet', 'group1']
        ]);
    }
}
