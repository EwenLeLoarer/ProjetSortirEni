<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieAnnulationType;
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
            //$sortie = $form->getData();
            $em->persist($sortie);
            $em->flush();

            //TODO change to the list of Sortie
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
            $this->addFlash('error', "Cette sortie est archivée");
            return $this->redirectToRoute('app_home');
        }
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[Route('/sortie/{id}/modify', name: 'app_sortie_modify', requirements: ['id'=>'\d+'])]
    public function modify(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
    {

        if($sortie->getEtat()->getId() == 7)
        {
            $this->addFlash('error', "cette sortie est archivé");
            return $this->redirectToRoute('app_home');
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
            $this->addFlash('error', "la date d'inscription est depassée");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getId() != 2)
        {
            $this->addFlash('error', "l'inscription n'est plus ouverte");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getNbInscriptionsMax() <= $sortie->getParticipants()->count())
        {
            $this->addFlash('error', "Le nombre d'inscrits est atteint");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());

        if (!$sortie->getParticipants()->contains($user)) {
            $sortie->addParticipant($user);
            $em->persist($sortie);
            $em->flush();
        } else {
            $this->addFlash('error', "utilisateur participe deja la sortie");
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
            $this->addFlash('error', "la sortie est commencé");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getId() > 2)
        {
            $this->addFlash('error', "l'inscription n'est plus ouverte ou en cours de creation");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());

        if($sortie->getParticipants()->contains($user)){
            $sortie->removeParticipant($user);
            $em->persist($sortie);
            $em->flush();
        } else {
            $this->addFlash('error', "utilisateur ne participe pas a la sortie");
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
            $this->addFlash('error', "la date d'inscription est depassé");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getId() == 4)
        {
            $this->addFlash('error', "la sortie est en cours");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }
        if($sortie->getEtat()->getId() == 5)
        {
            $this->addFlash('error', "la sortie est deja passé");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }
        if($sortie->getEtat()->getId() == 6)
        {
            $this->addFlash('error', "la sortie est deja annulé");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());

        if($user != $sortie->getOrganisateur() and !$this->isGranted('ROLE_ADMIN')){
            $this->addFlash('error', "l'utilisateur connecter n'est pas le createur de la sortie");
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
            $this->addFlash('error', "la date d'inscription est depassé");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        if($sortie->getEtat()->getId() != 1)
        {
            $this->addFlash('error', "la sortie n'est plus en preparation");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $user = $em->getRepository(Utilisateur::Class)->find($userConnected->getId());

        if($user != $sortie->getOrganisateur() ){
            $this->addFlash('error', "l'utilisateur connecter n'est pas le createur de la sortie");
            return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
        }

        $form = $this->createForm(SortieAnnulationType::class, $sortie);

            $sortie->setEtat($startedEtat);
            $em->persist($sortie);
            $em->flush();

        
        return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
    }
}