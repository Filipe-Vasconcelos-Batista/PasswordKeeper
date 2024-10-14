<?php

namespace App\Controller;

use App\Entity\PinCode;
use App\Form\PincodeType;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\BadConversionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PincodeController extends AbstractController
{
    #[Route('/pincode', name: 'app_pincode')]
    public function index(Request $request, EntityManagerInterface $manager ): Response
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
                $manager->persist($pinCode);
                $manager->flush();
                $this->addFlash('success', "Pincode added successfully");
            }catch (\Exception $e){
                $this->addFlash('error','Pincode cannot be saved', $e->getMessage());
            }
        }
        return $this->render('pincode/index.html.twig', [
            'controller_name' => 'PincodeController',
        ]);
    }
}
