<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
final class VilleController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/ville', name: 'app_ville')]
    public function index(VilleRepository $repo, Request $request): Response
    {
        $query = $request->query->get('query');
        $villes = $repo->findVilles($query);
        $form = $this->createForm(VilleType::class, new Ville(), [
            'action' => $this->generateUrl('app_create_ville'),
            'method' => 'POST',
        ]);
        $editForms = [];
        foreach ($villes as $v) {
            $editForms[$v->getId()] = $this->createForm(VilleType::class, $v, [
                'action' => $this->generateUrl('app_edit_ville', ['id' => $v->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView('ville/_table.html.twig', [
                'villes' => $villes,
                'form' => $form->createView(),
                'editForms' => $editForms,
            ]);
            return new Response($html);
        }
        return $this->render('ville/index.html.twig', [
            'villes' => $villes,
            'form' => $form->createView(),
            'editForms' => $editForms,
        ]);
    }
    #[Route('/ville/create', name: 'app_create_ville')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($ville);
            $em->flush();

            if($request->query->has('backToLieu')){
                return $this->redirectToRoute('app_lieu', [
                    'newVilleId' => $ville->getId(),
                ]);
            }
            if ($request->isXmlHttpRequest()) {
                return new Response('',204);
            }
            return $this->redirectToRoute('app_home');
        }

        return $this->render('ville/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/ville/{id}/edit', name: 'app_edit_ville')]
    public function edit(Request $request, EntityManagerInterface $em, Ville $ville): Response
    {
        $form = $this->createForm(VilleType::class, $ville)->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();

            if($request->isXmlHttpRequest()) {
                return new Response('',204);
            }

            return $this->redirectToRoute('app_ville');

        }
        return $this->render('ville/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/ville/{id}/delete', name: 'app_delete_ville')]
    public function delete(Request $request, Ville $ville, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_ville_'.$ville->getId(), $request->request->get('_token'))) {
            return $request->isXmlHttpRequest() ? new Response('', 400) : $this->redirectToRoute('app_ville');
        }

        $em->remove($ville);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new Response('',204);
        }

        return $this->redirectToRoute('app_ville');
    }
}
