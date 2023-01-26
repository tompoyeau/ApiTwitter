<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
