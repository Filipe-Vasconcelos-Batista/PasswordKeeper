<?php

namespace App\Controller;

use App\Entity\Password;
use App\Entity\PinCode;
use App\Form\PasswordGenerateType;
use App\Form\PasswordType;
use App\Service\PasswordGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Cache\CacheInterface;

class PasswordController extends AbstractController
{
    #[Route('/password', name: 'app_password_list')]
    public function index(EntityManagerInterface $manager,CacheInterface $cache): Response
    {
        $this->checkPinCode($manager);
        $user=$this->getUser();
        $passwords=$manager->getRepository(Password::class)->findBy(['user'=>$user]);
        $midAuth=$this->checkMidAuth($cache);
        return $this->render('password/index.html.twig', [
            'passwords'=> $passwords,
            'midAuth'=>$midAuth,
        ]);
    }
    #[Route('/password/generate', name: 'app_password_generate')]
    public function generatePassword(Request $request, PasswordGeneratorService $passwordGeneratorService, EntityManagerInterface $manager): Response{
        $password=new Password();
        $generated=null;
        $form= $this->createForm(PasswordType::class,$password);
        $generateForm=$this->createForm(PasswordGenerateType::class);
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
            'generatedPassword'=> $generated,
            'form'=> $form,
            'generateform'=> $generateForm
        ]);

    }

    #[Route('/password/individual/{id}', name: 'app_password_personal')]
    public function individual(EntityManagerInterface $manager, int $id): Response
    {

        $password=$manager->getRepository(Password::class)->findOneBy(['id'=> $id]);
        $this->checkUser($password);
        return $this->render('password/individual.html.twig', [
            'password'=> $password
        ]);
    }

    #[Route('/password/edit/{id}', name: 'app_password_edit')]
    public function editPassword(EntityManagerInterface $manager, Request $request,int $id,PasswordGeneratorService $passwordGeneratorService): Response
    {
        $password=$manager->getRepository(Password::class)->findOneBy(['id'=> $id]);
        $this->checkUser($password);
        $generated=null;
        $form= $this->createForm(PasswordType::class,$password);
        $generateForm=$this->createForm(PasswordGenerateType::class);
        $form->handleRequest($request);
        $generateForm->handleRequest($request);
        try{
            if($form->isSubmitted() && $form->isValid()){
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
        return $this->render('password/edit.html.twig', [
            'generatedPassword'=> $generated,
            'form'=> $form,
            'generateform'=> $generateForm,
            'password'=> $password
        ]);
    }
    #[Route('password/delete/{id}', name: 'app_password_delete')]
    public function deletePassword(EntityManagerInterface $manager, int $id): Response
    {
        $password=$manager->getRepository(Password::class)->findOneBy(['id'=> $id]);
        $this->checkUser($password);
        $manager->remove($password);
        $manager->flush();
        return $this->redirectToRoute('app_password_list');
    }

    private function checkUser(Password $password){
        if($password->getUser() !== $this->getUser()){
            throw new AccessDeniedException('Ups seems that you cannot touch this.');
        }
    }
    private function checkPinCode(EntityManagerInterface $manager): void{
        $user=$this->getUser();
        $pinCode= $manager->getRepository(PinCode::class)->findOneBy(['user'=>$user]);
        if(!$pinCode){
            $this->redirectToRoute('app_pincode')->send();
            exit;
        }
    }
    private function checkMidAuth(CacheInterface $cache): bool{
        $cacheItem= $cache->getItem('user_' . $this->getUser()->getId());
        $cacheData=$cacheItem->isHit() ? $cacheItem->get() : false;
        return isset($cacheData['midauth']) && $cacheData['midauth']===true;

    }
}
