<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class UtilisateurController extends AbstractController
{
    #[Route('/profil/edit', name: 'app_profil')]
    public function edit( Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, FileUploader $fileUploader): Response{
        $utilisateur = $this->getUser();
        $utilisateurForm = $this->createForm(UtilisateurType::class, $utilisateur);
        $utilisateurForm->handleRequest($request);
        if($utilisateurForm->isSubmitted() && $utilisateurForm->isValid()){
            $imageFile =$utilisateurForm->get('photo')->getData();
            if($imageFile){
                $utilisateur->setPhoto($fileUploader->upload($imageFile));
            }
            $plainPassword = $utilisateurForm->get('password')->getData();
            $passwordConfirmation = $utilisateurForm->get('passwordConfirmation')->getData();

            if ($plainPassword) {
                if ($plainPassword !== $passwordConfirmation) {
                    $utilisateurForm->get('passwordConfirmation')->addError(new FormError('Les mots de passe ne correspondent pas.'));
                } else{
                    $encodedPassword = $passwordHasher->hashPassword($utilisateur, $plainPassword);
                    $utilisateur->setPassword($encodedPassword);}
            }
            $em->flush();
            $this->addFlash('success', "Profil mis à jour");
            return $this->render('utilisateur/edit.html.twig', ['utilisateurForm' => $utilisateurForm, 'utilisateur' => $utilisateur]);
        }
        return $this->render('utilisateur/edit.html.twig', ['utilisateurForm' => $utilisateurForm, 'utilisateur' => $utilisateur]);
    }
    #[Route('/utilisateurs', name: 'app_utilisateurs')]
    public function show(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateurs = $utilisateurRepository->findAll();
        return $this->render('utilisateur/show.html.twig', ['utilisateurs' => $utilisateurs]);
    }
//    #[Route('/utilisateurs/{id}/edit', name: 'app_utilisateur', requirements: ['id' => '\d+'])]
//    public function edit(Utilisateur $utilisateur, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response{
//        $utilisateurForm = $this->createForm(UtilisateurType::class, $utilisateur);
//        $utilisateurForm->handleRequest($request);
//        if($utilisateurForm->isSubmitted() && $utilisateurForm->isValid()){
//            $plainPassword = $utilisateurForm->get('password')->getData();
//            $passwordConfirmation = $utilisateurForm->get('passwordConfirmation')->getData();
//
//            if ($plainPassword) {
//                if ($plainPassword !== $passwordConfirmation) {
//                    $utilisateurForm->get('passwordConfirmation')->addError(new FormError('Les mots de passe ne correspondent pas.'));
//                } else{
//                // encode password and set it
//                $encodedPassword = $passwordHasher->hashPassword($utilisateur, $plainPassword);
//                $utilisateur->setPassword($encodedPassword);}
//            }
//            $em->flush();
//            $this->addFlash('success', "Profil mis à jour");
//
//            return $this->redirectToRoute('utilisateurs_list');
//        }
//        return $this->render('utilisateur/edit.html.twig', ['utilisateurForm' => $utilisateurForm, 'utilisateur' => $utilisateur]);
//}
}
