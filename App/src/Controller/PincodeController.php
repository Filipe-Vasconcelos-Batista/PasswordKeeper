<?php

namespace App\Controller;

use App\Entity\PinCode;
use App\Form\PincodeType;
use App\Service\PincodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;

class PincodeController extends AbstractController
{
    private PincodeService $pincodeService;

    public function __construct(PincodeService $pincodeService)
    {
        $this->pincodeService = $pincodeService;
    }

    #[Route('/pincode', name: 'app_pincode')]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(PincodeType::class);
        $form->handleRequest($request);

        $user = $this->getUser();
        $pinCode = $manager->getRepository(PinCode::class)->findOneBy(['user' => $user]);
        if (!$pinCode) {
            $pinCode = new PinCode();
            $pinCode->setUser($user);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $hashedPinCode = hash('sha256', $form->get('pincode')->getData());
                $pinCode->setHashedPincode($hashedPinCode);
                $manager->persist($pinCode);
                $manager->flush();
                $this->addFlash('success', "Pincode added successfully");
            } catch (\Exception $e) {
                $this->addFlash('error', 'Pincode cannot be saved', $e->getMessage());
            }
            return $this->redirectToRoute("app_password_list");
        }
        return $this->render('pincode/index.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/pincode/insert', name: 'app_pincode_insert')]
    public function insert(Request $request, EntityManagerInterface $manager, CacheInterface $cache): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PincodeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $inputPinCode = $form->get('pincode')->getData();
                $hashedInputPinCode = hash('sha256', $inputPinCode);

                $pinCodeData = $manager->getRepository(PinCode::class)->findOneBy(['user' => $user]);

                if (!$pinCodeData) {
                    $this->addFlash('error', 'Pincode data not found');
                    return $this->render('pincode/index.html.twig', [
                        'form' => $form,
                    ]);
                }

                $storedHashedPinCode = $pinCodeData->getHashedPincode();

                if ($hashedInputPinCode === $storedHashedPinCode) {
                    try {
                        $item = $cache->getItem('user_' . $user->getId());
                        $item->expiresAfter(3600);
                        $item->set(['midauth' => true]);
                        $cache->save($item);
                        $this->addFlash('success', 'Pincode Inserted successfully');
                        return $this->redirectToRoute("app_password_list");
                    } catch (\Exception $cacheException) {
                        $this->addFlash('error', 'Cache error: ' . $cacheException->getMessage());
                    }
                } else {
                    $this->addFlash('error', 'Pincode doesn’t match');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Pincode doesn’t match: ' . $e->getMessage());
            }
        }

        return $this->render('pincode/index.html.twig', [
            'form' => $form,
        ]);
    }

}

