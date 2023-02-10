<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PizzaController extends AbstractController
{
    #[Route('/admin/pizza', name: 'app_admin_pizza')]
    public function index(): Response
    {
        return $this->render('admin/pizza/index.html.twig', [
            'controller_name' => 'PizzaController',
        ]);
    }
}
