<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class SiteController extends AbstractController
{
    #[Route('/site', name: 'app_site')]
    public function index(Request $request, SiteRepository $siteRepository): Response
    {
        $query = $request->query->get('query');
        $sites = $siteRepository->findSites($query);
        $form = $this->createForm(SiteType::class, new Site(), [
            'action' => $this->generateUrl('app_create_site'),
            'method' => 'POST',
        ]);
        $editForms = [];
        foreach ($sites as $site) {
            $editForms[$site->getId()] = $this->createForm(SiteType::class, $site, [
                'action' => $this->generateUrl('app_edit_site', ['id' => $site->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView('Site/_table.html.twig', [
                'sites' => $sites,
                'form' => $form->createView(),
                'editForms' => $editForms,
            ]);
            return new Response($html);
        }

        return $this->render('site/index.html.twig', [
            'sites' => $sites,
            'form' => $form->createView(),
            'editForms' => $editForms,
        ]);
    }

    #[Route('/site/create', name: 'app_create_site')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($site);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response('',204);
            }

            return $this->redirectToRoute('app_site');
        }

        return $this->render('site/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/site/{id}/edit', name: 'app_edit_site')]
    public function edit(Request $request, Site $site, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SiteType::class, $site)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($site);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response('',204);
            }

            return $this->redirectToRoute('app_site');
        }
        return $this->render('site/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/site/{id}/delete', name: 'app_delete_site')]
    public function delete(Request $request, Site $site, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_site_'.$site->getId(), $request->request->get('_token'))) {
            return $request->isXmlHttpRequest() ? new Response('',400) : $this->redirectToRoute('app_site');
        }

        $em->remove($site);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new Response('',204);
        }
        return $this->redirectToRoute('app_site');
    }
}