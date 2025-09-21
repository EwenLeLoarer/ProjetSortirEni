<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        if($request->query->has('newVilleId')){
            $ville = $em->getRepository(Ville::class)->find($request->query->get('newVilleId'));
            if($ville){

                $lieu->setVille($ville);
            }
        }

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

    // ajout de la route suivante pour mettre en place la récupération de ces champs dans le formulaire de création d'une sortie
    #[Route('/lieu/{id}', name: 'lieu_details')]
    public function lieuDeails(Lieu $lieu): JsonResponse
    {
        return new JsonResponse([
            'rue' => $lieu->getRue(),
            'codePostal' => $lieu->getVille()->getCodePostal(),
            'ville' => $lieu->getVille()->getNom(),
            'latitude' => $lieu->getLatitude(),
            'longitude' => $lieu->getLongitude(),
        ]);
    }
}
