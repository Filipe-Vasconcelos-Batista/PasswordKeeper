<?php

namespace App\Controller;

use App\Entity\Password;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PasswordController extends AbstractController
{
    #[Route('/password', name: 'app_password')]
    public function index(EntityManagerInterface $manager): Response
    {
        $password=$manager->getRepository(Password::class)->findAll();
        return $this->render('password/index.html.twig', [
            'password'=> $password
        ]);
    }
}
