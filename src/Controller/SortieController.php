<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    public function new(Request $Request): Response
    {
        $sortie = new Sortie();

        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($Request);

        if($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();

            //TODO change to the list of Sortie
            return $this->redirectToRoute('app_main');
        }

        return $this->render('sortie/new.html.twig', [
            'form' => $form
        ]);

    }


}
