<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VilleController extends AbstractController
{
    #[Route('/ville/create', name: 'app_create_ville')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Ville();

        $form = $this->createForm(VilleType::class, $ville);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($ville);
            $em->flush();

            if($request->query->has('backToLieu')){
                return $this->redirectToRoute('app_lieu', [
                    'newVilleId' => $ville->getId(),
                ]);
            }

            return $this->redirectToRoute('app_home');
        }




        return $this->render('ville/index.html.twig', [
            'form' => $form,
        ]);
    }
}
