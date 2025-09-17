<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, SortieRepository $sortiesRepository, SiteRepository $siteRepository): Response
    {
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

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sorties' => $sorties,
            'sites' => $sites,
            'filters' => $filters,
        ]);
    }
}