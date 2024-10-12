<?php

namespace App\Controller;

use App\Entity\PinCode;
use App\Form\PincodeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $pincode=new Pincode();
        if($form->isSubmitted() && $form->isValid()){
            try{
                $pincode->setUser($this->getUser());
                $manager->persist($pincode);
            }catch (\Exception $e){
                $this->addFlash('error','Pincode cannot be saved', $e->getMessage());
            }
        }
        return $this->render('pincode/index.html.twig', [
            'controller_name' => 'PincodeController',
        ]);
    }
}
