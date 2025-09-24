<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieAnnulationType;
use App\Form\SortieType;
use App\Service\SortieService;
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
    #[Route('/sortie', name: 'app_sortie', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();

        $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'En création']);
        if (!$etat) {
            throw $this->createNotFoundException('Etat "En création" not found.');
        }
        $sortie->setEtat($etat);

        $userConnected = $this->getUser();
        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());
        if ($request->query->has('newLieuId')){
            $lieu = $em->getRepository(Lieu::Class)->find($request->query->get('newLieuId'));
            if($lieu){
                $sortie->setLieu($lieu);
            }
        }
        $form = $this->createForm(SortieType::class, $sortie, [
            'organisateur' => $user,
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $sortie->setOrganisateur($user);
            $sortie->addParticipant($user);
            if ($form->get('publier')->isClicked()) {
                if($sortie->getDateHeureDebut() > new \DateTime()){
                    $etatOuvert = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
                    if ($etatOuvert) {
                        $sortie->setEtat($etatOuvert);
                    }
                }
                $this->addFlash('success', 'Sortie publiée avec succès !');
            } else {
                 $this->addFlash('success', 'Sortie enregistrée avec succès !');
            }

            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/index.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/sortie/{id}', name: 'app_sortie_show', requirements: ['id'=>'\d+'])]
    public function show(Sortie $sortie): Response
    {

        if($sortie->getEtat()->getId() == 7)
        {
            $this->addFlash('error', "Cette sortie est archivée.");
            return $this->redirectToRoute('app_home');
        }
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[Route('/sortie/{id}/modify', name: 'app_sortie_modify', requirements: ['id'=>'\d+'])]
    public function modify(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if($sortie->getEtat()->getId() == 7)
        {
            $this->addFlash('error', "Cette sortie est archivée.");
            return $this->redirectToRoute('app_home');
        }

        if($user != $sortie->getOrganisateur() and !$this->isGranted('ROLE_ADMIN')){
            $this->addFlash('error', "L'utilisateur n'a pas les droits nécessaires pour modifier la sortie.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }
        $form = $this->createForm(SortieType::class, $sortie, []);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }


        return $this->render('sortie/modify.html.twig', [
            'form' => $form,
            'sortie' => $sortie
        ]);
    }


    #[Route('/sortie/{id}/register', name: 'app_sortie_register', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function register(Sortie $sortie, EntityManagerInterface $em): Response
    {

        $userConnected = $this->getUser();

        if(!$userConnected){
            throw $this->createAccessDeniedException('You must be logged in to register.');
        }

        if($sortie->getDateLimiteInscription() < new \DateTime()){
            $this->addFlash('error', "La date d'inscription est dépassée.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getId() != 2)
        {
            $this->addFlash('error', "L'inscription n'est plus ouverte.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getNbInscriptionsMax() <= $sortie->getParticipants()->count())
        {
            $this->addFlash('error', "Le nombre d'inscrits est atteint.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());

        if (!$sortie->getParticipants()->contains($user)) {
            $sortie->addParticipant($user);
            $em->persist($sortie);
            $em->flush();
        } else {
            $this->addFlash('error', "L'utilisateur participe déjà à la sortie");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
    }

    #[Route('/sortie/{id}/unregister', name: 'app_sortie_unregister', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function unregister(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $userConnected = $this->getUser();

        if(!$userConnected){
            throw $this->createAccessDeniedException('You must be logged in to unregister.');
        }

        if($sortie->getDateHeureDebut() < new \DateTime()){
            $this->addFlash('error', "La sortie a déjà commencé.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getId() > 2)
        {
            $this->addFlash('error', "L'inscription n'est plus ouverte ou est en cours de création.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());

        if($sortie->getParticipants()->contains($user)){
            $sortie->removeParticipant($user);
            $em->persist($sortie);
            $em->flush();
        } else {
            $this->addFlash('error', "L'utilisateur ne participe pas à la sortie");
        }

        return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);

    }

    #[Route('/sortie/{id}/cancel', name: 'app_sortie_cancel', requirements: ['id'=>'\d+'])]
    public function GetCancel(Sortie $sortie, EntityManagerInterface $em, Request $request): Response
    {
        $userConnected = $this->getUser();
        $cancelEtat = $em->getRepository(Etat::Class)->find(6);
        if(!$userConnected){
            throw $this->createAccessDeniedException('You must be logged in to cancel.');
        }

        if($sortie->getDateHeureDebut() < new \DateTime()){
            $this->addFlash('error', "La date d'inscription est dépassée.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getId() == 4)
        {
            $this->addFlash('error', "La sortie est en cours.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }
        if($sortie->getEtat()->getId() == 5)
        {
            $this->addFlash('error', "la sortie est déjà passée.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }
        if($sortie->getEtat()->getId() == 6)
        {
            $this->addFlash('error', "la sortie est déjà annulée.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());

        if($user != $sortie->getOrganisateur() and !$this->isGranted('ROLE_ADMIN')){
            $this->addFlash('error', "L'utilisateur n'a pas les droits pour supprimer la sortie.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $form = $this->createForm(SortieAnnulationType::class, $sortie);

        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid()){
            $sortie->setEtat($cancelEtat);
            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/cancel.html.twig', [
            'sortie' => $sortie,
            'form' => $form
        ]);

    }

    #[Route('/sortie/{id}/publish', name: 'app_sortie_publish', requirements: ['id'=>'\d+'])]
    public function publish(Sortie $sortie, EntityManagerInterface $em, Request $request): Response
    {
        $userConnected = $this->getUser();
        $startedEtat = $em->getRepository(Etat::Class)->find(2);
        if(!$userConnected){
            throw $this->createAccessDeniedException('You must be logged in to cancel.');
        }

        if($sortie->getDateHeureDebut() < new \DateTime()){
            $this->addFlash('error', "La date d'inscription est dépassée.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getId() != 1)
        {
            $this->addFlash('error', "La sortie n'est plus en préparation.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());

        if($user != $sortie->getOrganisateur() ){
            $this->addFlash('error', "L'utilisateur connecté n'est pas le créateur de la sortie.");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $form = $this->createForm(SortieAnnulationType::class, $sortie);

        $sortie->setEtat($startedEtat);
        $em->persist($sortie);
        $em->flush();
        $this->addFlash('success', "La sortie a bien été publiée.");
        return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
    }
}