<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UtilisateurController extends AbstractController
{
        #[Route('/profil/edit', name: 'app_profil')]
        public function edit(Request $request, EntityManagerInterface $em,
                     UserPasswordHasherInterface $passwordHasher, FileUploader $fileUploader): Response
    {
        $utilisateur = $this->getUser();

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        $plain = $form->get('password')->getData();
        $confirm = $form->get('passwordConfirmation')->getData();

        if ($plain && $plain !== $confirm) {
            $form->get('passwordConfirmation')->addError(new FormError('Les mots de passe ne correspondent pas.'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if ($file = $form->get('photo')->getData()) {
                $utilisateur->setPhoto($fileUploader->upload($file));
            }
            if ($plain) {
                $utilisateur->setPassword($passwordHasher->hashPassword($utilisateur, $plain));
            }

            $em->persist($utilisateur);
            $em->flush();

            $this->addFlash('success', "La mise à jour du profil a bien été prise en compte.");
            return $this->redirectToRoute('app_profil');
        }
        elseif ($form->isSubmitted()) {
            $this->addFlash('error', "Le profil n'a pas pu être mis à jour.");
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateurForm' => $form,
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/utilisateur/{id}', name: 'app_utilisateur', requirements: ['id' => '\d+'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
}
