<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Utilisateur;
use App\Form\CsvImportType;
use App\Form\UtilisateurType;
use App\Repository\SiteRepository;
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

    #[Route('/admin/createUser', name: 'app_create_user')]
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
    #[Route('/admin/importUser', name: 'app_import_user')]
    public function importUsers(Request $Request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(CsvImportType::class);
        $form->handleRequest($Request);

        if($form->isSubmitted() && $form->isValid()){
            $csvFile = $form->get('csvFile')->getData();

            if($csvFile){
                $handle = fopen($csvFile->getPathName(), "r");

                fgetcsv($handle);

                while(($data = fgetcsv($handle, 1000, ';')) !== false){
                    [$email, $roles, $password, $prenom, $nom, $telephone, $site, $is_Actif, $pseudo, $photo] = $data;

                    $roles = ['ROLE_USER'];
                    $user = new Utilisateur();
                    $user->setEmail($email);
                    $user->setRoles($roles);
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                    $user->setPrenom($prenom);
                    $user->setNom($nom);
                    $user->setTelephone($telephone);
                    $user->setSite($em->getRepository(site::class)->findOneBy(['id' => $site]));
                    $user->setIsActif($is_Actif);
                    $user->setPseudo($pseudo);
                    $user->setPhoto($photo);

                    $em->persist($user);
                }
                fclose($handle);

                $em->flush();
                $this->addFlash('success', 'Utilisateur importé avec success');
                return $this->redirectToRoute('app_admin');
            }

        }
        return $this->render('admin/import_users.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/listUser', name: 'app_list_user')]
    public function listUsers(EntityManagerInterface $em) : Response
    {
        $users = $em->getRepository(Utilisateur::class)->findAll();

        return $this->render('admin/list_user.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/{id}/desativate', name: 'app_desactivate_user', requirements: ['id' => '\d+'])]
    public function desactivateUser(Utilisateur $user, EntityManagerInterface $em) : Response
    {
        $user->setIsActif(false);

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été désactivé.');
        return $this->redirectToRoute('app_list_user');
    }

    #[Route('/admin/{id}/delete', name: 'app_delete_user', requirements: ['id' => '\d+'])]
    public function deleteUser(Utilisateur $user, EntityManagerInterface $em) : Response
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé.');
        return $this->redirectToRoute('app_list_user');
    }
}
