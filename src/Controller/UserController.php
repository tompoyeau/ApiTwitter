<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/user', name: 'app_user', methods: 'GET')]
    public function index(UserRepository $UserRepo, SerializerInterface $serializer) : Response
    {
        $userList = $UserRepo->findAll();
        $jsonContent = $serializer->serialize($userList, 'json');

        return new Response($jsonContent);
    }

//     class UserController extends AbstractController
// {
//     public function __construct(private UserRepository $userRepo) {}

//     #[Route('/api/user', name: 'app_user', methods: 'GET')]
//     public function index(): JsonResponse
//     {
//         $users = $this->userRepo->findAll();
//         return $this->json($users, context: ['groups' => ["user"]]);
//     }
// }
}
