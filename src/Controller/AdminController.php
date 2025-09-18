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

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/createUser', name: 'app_admin')]
    public function createUser(Request $Request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, FileUploader $fileUploader): Response
    {
        $user = new Utilisateur();

        $form = $this->createForm(UtilisateurType::class, $user);

        $form->handleRequest($Request);
        if($form->isSubmitted() && $form->isValid()){
            $imageFile = $form->get('photo')->getData();
            if($imageFile){
                $user->setPhoto($fileUploader->upload($imageFile));
            }
            $plainPassword = $form->get('password')->getData();
            $passwordConfirmation = $form->get('passwordConfirmation')->getData();

            if($plainPassword){
                if($plainPassword !== $passwordConfirmation){
                    $form->get('passwordConfirmation')->addError(new FormError("les mots de passe ne correspondent pas"));
                } else {
                    $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($encodedPassword);
                }
            }
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();
            $this->addFlash('succes', "L'utilisateur a bien été rajouté");
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/create_user.html.twig', [
            'form' =>  $form
        ]);
    }

}
