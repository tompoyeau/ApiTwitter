<?php

namespace App\Controller;

use App\Repository\TweetRepository;
use App\Entity\Tweet;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

class TweetController extends AbstractController
{
    /**
     * Liste les tweets de la base de données.
     *
     * Récupère et renvoie sous format json l'id et email de chaque user présent dans la bdd.
     *
     * @Route("/api/tweets", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Tweets found",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Tweet::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Tweets")
     */
    public function AllTweets(TweetRepository $repository): JsonResponse
    {
        $tweets = $repository->findAll();
        return $this->json($tweets, 200, [], [
            "groups" => ["groupTweet"]

        ]);
    }

    /**
     * Renvoie un tweet via son id.
     *
     * Récupère et renvoie sous format json l'id, le texte, la date et l'id user dont l'id du tweet est passé en paramètre GET.
     *
     * @Route("/api/tweet/{id}", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Tweet found",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Tweet::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Tweets")
     */
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
            "groups" => ["groupTweet"]

        ]);
    }

    /**
     * Supprime un tweet via son id.
     *
     * Récupère et renvoie sous format json l'id, le texte, la date et l'id user dont l'id du tweet est passé en paramètre GET.
     *
     * @Route("/api/tweet/{id}", methods={"DELETE"})
     * @OA\Response(
     *     response=200,
     *     description="Tweet found",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Tweet::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Tweets")
     */
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $tweet = $entityManager->getRepository(Tweet::class)->find($id);
        if (!$tweet) {
            throw $this->createNotFoundException(
                'No user found for id ' . $id
            );
        }
        $entityManager->remove($tweet);
        $entityManager->flush();
        return $this->json(null, 204);
    }
}
