<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Faker;
use App\Entity\User;
class HelloWorldController extends AbstractController
{
    #[Route('/hello-world', name: 'app_hello_world')]
    public function index(): JsonResponse
    {
        $faker = Faker\Factory::create('fr_FR');
        $user = new User();
        $user->setEmail($faker->email());
        // $em = $this->getDoctrine()->getManager();
        // $em->persist($user);
        // $em->flush();
        
        return $this->json([
            'message' => $user->getEmail(),
            'path' => 'src/Controller/HelloWorldController.php',
        ]);
    }
}