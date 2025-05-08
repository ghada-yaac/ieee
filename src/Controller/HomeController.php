<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home/pending', name: 'app_home_pending')]
    public function pending(): Response
    {
        return $this->render('home/pending.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/home/member', name: 'app_home_member')]
    public function home(): Response
    {
        return $this->render('home/acceuil.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
