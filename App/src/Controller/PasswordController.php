<?php

namespace App\Controller;

use App\Entity\Password;
use App\Entity\PinCode;
use App\Form\PasswordGenerateType;
use App\Form\PasswordType;
use App\Service\CacheService;
use App\Service\PasswordGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PasswordController extends AbstractController
{
    private CacheService $cache;

    private PasswordGeneratorService $passwordGenerator;

    private string $secret;

    public function __construct(CacheService $cacheService, PasswordGeneratorService $passwordGenerator)
    {
        $this->cache = $cacheService;
        $this->passwordGenerator = $passwordGenerator;
        $this->secret = $_ENV['ENCRYPT_KEY'];

    }
    #[Route('/password', name: 'app_password_list')]
    public function index(EntityManagerInterface $manager): Response
    {
        $this->checkPinCode($manager);
        $user=$this->getUser();
        $passwords=$manager->getRepository(Password::class)->findBy(['user'=>$user]);
        foreach($passwords as $password){
            $original=$password->getPassword();
            $password->setPassword($this->decryptPassword($original));
        }
        $midAuth=$this->cache->getAuth('midauth' , $user->getId());
        $highAuth=$this->cache->getAuth('highauth' , $user->getId());;
        return $this->render('password/index.html.twig', [
            'passwords'=> $passwords,
            'midAuth'=>$midAuth,
            'highAuth'=>$highAuth,
        ]);
    }

    #[Route('/password/generate', name: 'app_password_generate')]
    public function generatePassword(Request $request, EntityManagerInterface $manager): Response{
        $password=new Password();
        $generated=null;
        $SaveForm= $this->createForm(PasswordType::class,$password);
        $generateForm=$this->createForm(PasswordGenerateType::class);
        $SaveForm->handleRequest($request);
        $generateForm->handleRequest($request);
        try{
            if($SaveForm->isSubmitted() && $SaveForm->isValid()){
                $password->setUser($this->getUser());
                $password->setPassword($this->encryptPassword($SaveForm->get("password")->getData()));
                $manager->persist($password);
                $manager->flush();
                $this->addFlash('success', 'Password saved');
                $this->redirectToRoute('app_password_list');
            }
            elseif($generateForm->isSubmitted() && $generateForm->isValid()){
                $formData=$generateForm->getData();
                $generated=$this->generateNewPassword($formData);
            }
        }catch(\Exception $e){
            $this->addFlash('error','Your password could not be generated', $e->getMessage());
        };
        return $this->render('password/generate.html.twig', [
            'generatedPassword'=> $generated,
            'form'=> $SaveForm,
            'generateform'=> $generateForm
        ]);

    }

    #[Route('/password/individual/{id}', name: 'app_password_personal')]
    public function individual(EntityManagerInterface $manager, int $id): Response
    {
        $password=$manager->getRepository(Password::class)->findOneBy(['id'=> $id]);

        $this->checkUser($password);
        $original=$password->getPassword();
        $password->setPassword($this->decryptPassword($original));
        return $this->render('password/individual.html.twig', [
            'password'=> $password
        ]);
    }

    #[Route('/password/edit/{id}', name: 'app_password_edit')]
    public function editPassword(EntityManagerInterface $manager, Request $request,int $id): Response
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
                $original=$form->get('password')->getData();
                $password->setPassword($this->encryptPassword($original));
                $manager->persist($password);
                $manager->flush();

            }
            elseif($generateForm->isSubmitted() && $generateForm->isValid()){
                $formData=$generateForm->getData();
                $generated=$this->generateNewPassword($formData);
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

    private function generateNewPassword($formData):string{
        $length=$formData['length'];
        $numbers=$formData['numbers'];
        $special=$formData['specialk'];
        return $this->passwordGenerator->generatePassword($length,$numbers,$special);
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
    private function encryptPassword(string $password):string{
        $iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedPassword=openssl_encrypt($password,'aes-256-cbc',$this->secret,0,$iv);
        return base64_encode($encryptedPassword . '::' . $iv);
    }
    private function decryptPassword(string $password){
        list($encryptedData, $iv)=explode('::' , base64_decode($password),2);
        return openssl_decrypt($encryptedData,'aes-256-cbc', $this->secret,0,$iv);
    }
}
