<?php

namespace App\Controller;

use App\Entity\Password;
use App\Form\PasswordGenerateType;
use App\Form\PasswordType;
use App\Service\PasswordGeneratorService;
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
        $formInsert= $this->createForm(PasswordType::class,$password);
        $formInsert->handleRequest($request);
        try{

            if($formInsert->isSubmitted() && $formInsert->isValid()){
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
            'form'=> $formInsert
        ]);
    }
    #[Route('/password/generate', name: 'app_password_generate')]
    public function generatePassword(Request $request, PasswordGeneratorService $passwordGeneratorService, EntityManagerInterface $manager): Response{
        $password=new Password();
        $generated=null;
        $form= $this->createForm(PasswordType::class,$password);
        $generateForm=$this->createForm(PasswordGenerateType::class);
$special=null;
$numbers=null;
        $form->handleRequest($request);
        $generateForm->handleRequest($request);
        try{
            if($form->isSubmitted() && $form->isValid()){
                $password->setUser($this->getUser());
                $manager->persist($password);
                $manager->flush();
            }
            elseif($generateForm->isSubmitted() && $generateForm->isValid()){
                $formData=$generateForm->getData();
                $length=$formData['length'];
                $numbers=$formData['numbers'];
                $special=$formData['specialk'];
                $generated=$passwordGeneratorService->generatePassword($length,$numbers,$special);
            }
        }catch(\Exception $e){
            $this->addFlash('error','Your password could not be generated', $e->getMessage());
        };
        return $this->render('password/generate.html.twig', [
            'special'=>$special,
            'number'=>$numbers,
            'generatedPassword'=> $generated,
            'form'=> $form,
            'generateform'=> $generateForm
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
