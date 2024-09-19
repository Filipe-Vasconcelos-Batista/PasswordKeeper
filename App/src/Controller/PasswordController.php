<?php

namespace App\Controller;

use App\Entity\Password;
use App\Form\PasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/password/insert', name: 'app_password_insert')]
    public function insert(Request $request,EntityManagerInterface $manager): Response
    {
        $form= $this->createForm(PasswordType::class);
        try{
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $newPass=$form->getData();
                $password=new Password();
                $password->setPassword($newPass->getPassword());
                $password->setDescription($newPass->getDescription());
                $password->setLocal($newPass->getLocal());

                $manager->persist($newPass);
                $manager->flush();

                $this->addFlash('success', 'Password saved successfully!');
            }
        }catch(\Exception $e){
            $this->addFlash('error', $e->getMessage());
        }
        return $this->render('password/insert.html.twig', [
            'form'=> $form
        ]);
    }

    #[Route('/password/personal/{id}', name: 'app_password_personal')]
    public function individual(EntityManagerInterface $manager, int $id): Response
    {
        $password=$manager->getRepository(Password::class)->findAll();
        return $this->render('password/index.html.twig', [
            'password'=> $password
        ]);
    }

}
