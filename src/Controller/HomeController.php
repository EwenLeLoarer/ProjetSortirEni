<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Detection\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, SortieRepository $sortiesRepository, SiteRepository $siteRepository): Response
    {
        $detect = new MobileDetect();
        $isMobile = $detect->isMobile() && !$detect->isTablet();
        $utilisateur = $this->getUser();
        $filters = [
            'site' => $request->query->get('site'),
            'query' => $request->query->get('query'),
            'from' => $request->query->get('from'),
            'to' => $request->query->get('to'),
            'organisees' => $request->query->get('organisees'),
            'prevues' => $request->query->get('prevues'),
            'passees' => $request->query->get('passees'),
        ];

        $user = $this->getUser();
        $sorties = $sortiesRepository->search($filters, $user);
        $sites = $siteRepository->findAll();

        if ($detect->isMobile() && !$detect->isTablet()) {
            return $this->render('mobile/index.html.twig', [
                'sorties' => $sorties,
                'utilisateur' => $utilisateur,
                'sites' => $sites,
                'isMobile' => $isMobile,
            ]);
        }
        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView('sortie/_table.html.twig', [
                'sorties'    => $sorties,
                'utilisateur' => $utilisateur,
                'sites' => $sites,
                'filters' => $filters
            ]);
            return new Response($html);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sorties' => $sorties,
            'sites' => $sites,
            'filters' => $filters,
        ]);
    }
}