<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $user = $this->getUser();
        $form = $this->createForm(SortieType::class, $sortie, [
            'organisateur' => $user,
        ]);

        if ($request->query->has('newLieuId')){
            $lieu = $em->getRepository(Lieu::Class)->find($request->query->get('newLieuId'));
            if($lieu){
                $sortie->setLieu($lieu);
            }
        }

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $sortie->setOrganisateur($user);
            //$sortie = $form->getData();
            $em->persist($sortie);
            $em->flush();

            //TODO change to the list of Sortie
            return $this->redirectToRoute('app_home');
        }

        return $this->render('sortie/index.html.twig', [
            'form' => $form
        ]);

    }


}
