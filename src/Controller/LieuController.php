<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'app_lieu')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $lieu = new Lieu();

        $form = $this->createForm(LieuType::class, $lieu);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($lieu);
            $em->flush();

            if($request->query->has('backToSortie')){
                return $this->redirectToRoute('app_sortie', [
                    'newLieuId' => $lieu->getId(),
                ]);
            }

            return $this->redirectToRoute('app_home');

        }


        return $this->render('lieu/create.html.twig', [
            'form' => $form
        ]);
    }
}
