<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieEventController extends AbstractController
{
    #[Route('/Categorie/event', name: 'app_Categorie_event')]
    public function index(): Response
    {
        return $this->render('Categorie_event/index.html.twig', [
            'controller_name' => 'CategorieEventController',
        ]);
    }
}
