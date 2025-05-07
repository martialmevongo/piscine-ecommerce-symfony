<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class acceuilController extends AbstractController
{
    #[Route('/', name: 'acceuil')]
    public function displayHome()
    {
        return $this->render('acceuil.html.twig', [
            'titre' => 'Le ecommerce de la piscine',
        ]);
    }
}