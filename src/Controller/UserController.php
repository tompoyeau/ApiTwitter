<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    /**
     * Liste les users de la base de données.
     *
     * Récupère et renvoie sous format json l'id et email de chaque user présent dans la bdd.
     *
     * @Route("/api/users", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Users found",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     */
    public function AllUsers(UserRepository $repository): JsonResponse
    {
        $users = $repository->findAll();
        return $this->json($users, 200, [], [
            "groups" => ["groupUser"]

        ]);
    }

    /**
     * Renvoie un user via son id.
     *
     * Récupère et renvoie sous format json l'id et email d'un user dont l'id est passé en paramètre GET.
     *
     * @Route("/api/user/{id}", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="User found",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     */
    public function show(EntityManagerInterface $entityManager, int $id, SerializerInterface $serializer): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id ' . $id
            );
        }
        $jsonContent = $serializer->serialize($user, 'json', [
            'groups' => ['groupUser'],
        ]);
        return $this->json($user, 200, [], [
            "groups" => ["groupUser"]

        ]);
    }

    /**
     * Suppression d'un user via son id.
     *
     * Récupère et renvoie sous format json l'id et email d'un user dont l'id est passé en paramètre GET.
     *
     * @Route("/api/user/{id}", methods={"DELETE"})
     * @OA\Response(
     *     response=200,
     *     description="User found",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     */
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id ' . $id
            );
        }
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->json(null, 204);
    }

    /**
     * Création d'un nouveau user
     *
     * Créer un nouveau user à partir des paramètres envoyés en POST.
     *
     * @Route("/api/user/create", methods={"POST"})
     * @OA\Response(
     *     response=201,
     *     description="User created",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     */
    public function createUser(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
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

        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setRoles(['utilisateur']);
        $user->setPassword($data['password']);

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue lors de la création de l\'utilisateur.'], 500);
        }
        return $this->json($user, 201, [], [
            'groups' => ['groupUser']
        ]);
    }
}
