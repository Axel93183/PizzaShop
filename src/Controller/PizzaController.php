<?php

namespace App\Controller;

use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PizzaController extends AbstractController
{
    #[Route('/home', name: 'app_pizza_home')]
    public function home(PizzaRepository $repository): Response
    {
        
        $pizzas = $repository->findAll();


        return $this->render('pizza/home.html.twig', [
            'pizzas' => $pizzas,
        ]);
    }
}
