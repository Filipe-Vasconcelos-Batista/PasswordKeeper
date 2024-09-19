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
    #[Route('/password', name: 'app_password_list')]
    public function index(EntityManagerInterface $manager): Response
    {
        $user=$this->getUser();
        $passwords=$manager->getRepository(Password::class)->findBy(['user'=>$user]);
        return $this->render('password/index.html.twig', [
            'passwords'=> $passwords
        ]);
    }
    #[Route('/password/insert', name: 'app_password_insert')]
    public function insert(Request $request,EntityManagerInterface $manager): Response
    {
        $password=new Password();
        $form= $this->createForm(PasswordType::class,$password);
        try{
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $password->setUser($this->getUser());

                $manager->persist($password);
                $manager->flush();

                $this->addFlash('success', 'Password saved successfully!');
                return $this->redirectToRoute('app_password_list');
            }
        }catch(\Exception $e){
            $this->addFlash('error', $e->getMessage());
        }
        return $this->render('password/insert.html.twig', [
            'form'=> $form
        ]);
    }

    #[Route('/password/individual/{id}', name: 'app_password_personal')]
    public function individual(EntityManagerInterface $manager, int $id): Response
    {

        $password=$manager->getRepository(Password::class)->findOneBy(['id'=> $id]);
        if($password->getUser() !== $this->getUser()){
            return new Response('Ups seems that you cannot see this.', Response::HTTP_FORBIDDEN);
    }
        return $this->render('password/individual.html.twig', [
            'password'=> $password
        ]);
    }

}
