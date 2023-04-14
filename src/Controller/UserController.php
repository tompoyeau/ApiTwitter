<?php

namespace App\Controller;

use App\Entity\User;
use App\DTO\UserDTO;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{

    #[Route('/api/users', name: 'users_list', methods: 'GET')]
    #[OA\Get(
        summary: 'Retourne la liste des utilisateurs',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des utilisateurs',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: User::class, groups: ['groups' => 'groupUser']))
                )
            )
        ]
    )]
    #[OA\Tag(name: "Users")]
    public function AllUsers(UserRepository $repository): JsonResponse
    {
        $users = $repository->findAll();
        return $this->json(data:$users, context:[
            "groups" => ["groupUser"]

        ]);
    }

    #[Route('/api/user/{id}', name: 'find_user', methods: 'GET')]
    #[OA\Get(
        summary: 'Retourne un utilisateur',
        responses: [
            new OA\Response(
                response: 200,
                description: 'L\'utilisateur',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: User::class, groups: ['groups' => 'groupUser']))
                )
            )
        ]
    )]
    #[OA\Tag(name: "Users")]
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

    #[Route('/api/user/{id}', name: 'user_delete', methods: 'DELETE')]
    #[OA\Delete(
        summary: 'Suppression d\'un utilisateur',
        responses: [
            new OA\Response(
                response: 204,
                description: 'Utilisateur supprimé',
            )
        ]
    )]
    #[OA\Tag(name: "Users")]
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

    #[Route("/api/user/{id}", name: "user_update", methods: "PUT")]
    #[OA\Put(
        summary: 'Mise à jour des données user',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Informations d\'un utilisateur',
            content: new OA\JsonContent(
                ref: new Model(type: UserDto::class)
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Identifiant de l\'utilisateur'
            ),
        ]
    )]
    #[OA\Tag(name: 'Users')]
    public function updateUserById(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $hasher, SerializerInterface $serializer, int $id): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user == null) {
            return $this->json('L\'utilisateur n\'existe pas');
        }

        try {
            $userDto = $serializer->deserialize($request->getContent(), UserDto::class, 'json');
        } catch (Exception $e) {
            return $this->json($e->getMessage());
        }

        $errors = $validator->validate($userDto);

        if (count($errors) > 0) {
            return $this->json((string) $errors);
        }

        $user->setEmail($userDto->email)
            ->setUsername($userDto->username)
            ->setPassword($hasher->hashPassword($user, $userDto->password));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json("Modification réussi");
    }

    #[Route('/api/user/create', name: 'create_user', methods: 'POST')]
    #[OA\Post(
        summary: 'Création d\'un utilisateur',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Informations d\'un utilisateur',
            content: new OA\JsonContent(
                ref: new Model(type: UserDto::class)
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Identifiant de l\'utilisateur'
            ),
        ]
    )]
    #[OA\Tag(name: "Users")]
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $hasher, SerializerInterface $serializer): JsonResponse
    {
        try {
            $userDto = $serializer->deserialize($request->getContent(), UserDto::class, 'json');
        } catch (Exception $e) {
            return $this->json($e->getMessage(), 400);
        }

        $errors = $validator->validate($userDto);

        if (count($errors) > 0) {
            return $this->json((string) $errors, 400);
        }

        $user = new User();
        $user->setEmail($userDto->email)
            ->setUsername($userDto->username)
            ->setPassword($hasher->hashPassword($user, $userDto->password));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json("Identifiant de l'utilisateur : {$user->getId()}");
    }
}
