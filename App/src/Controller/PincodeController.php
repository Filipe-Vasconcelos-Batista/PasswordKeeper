<?php

namespace App\Controller;

use App\Entity\PinCode;
use App\Entity\User;
use App\Form\PincodeType;
use App\Service\PincodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PincodeController extends AbstractController
{
    private PincodeService  $pincodeService;
    public function __construct(PincodeService $pincodeService){
        $this->pincodeService = $pincodeService;
    }
    #[Route('/pincode', name: 'app_pincode')]
    public function index(Request $request, EntityManagerInterface $manager,UserPasswordHasherInterface $hasher): Response
    {

        $form=$this->createForm(PincodeType::class);
        $form->handleRequest($request);

        $user=$this->getUser();
        $pinCode=$manager->getRepository(PinCode::class)->findOneBy(['user'=>$user]);
        if(!$pinCode){
            $pinCode=new PinCode();
            $pinCode->setUser($user);
        }
        if($form->isSubmitted() && $form->isValid()){
            try{
                $hashedPinCode=$hasher->hashPassword($user,$form->get('pincode')->getData());
                $pinCode->setHashedPincode($hashedPinCode);
                $manager->persist($pinCode);
                $manager->flush();
                $this->addFlash('success', "Pincode added successfully");
            }catch (\Exception $e){
                $this->addFlash('error','Pincode cannot be saved', $e->getMessage());
            }
            return $this->redirectToRoute("app_password_list");
        }
        return $this->render('pincode/index.html.twig', [
            'form' => $form,
        ]);
    }
}
