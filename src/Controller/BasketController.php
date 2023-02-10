<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Pizza;
use App\Entity\Article;
use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\BasketRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
class BasketController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_basket_display')]
    public function display(): Response
    {
        return $this->render('basket/display.html.twig');
    }

    #[Route('/mon-panier/{id}/ajouter', name: 'app_basket_addArticle')]
    public function addArticle(Pizza $pizza, BasketRepository $repository): Response
    {
        //récup l'utilisateur
        $user=$this->getUser();
        //recup du panier de cet utilisateur
        $basket= $user->getBasket();

        // Creer un nouvel artcile a mettre dans le panier 
        $article= new Article();
        $article->setQuantity(1);//parametrer la quantité à "1"

        $article->setBasket($basket);//relie l'article au panier
        $article->setPizza($pizza);//relie l'article a la pizza choisie

        //ajouter l'article au panier
        $basket->addArticle($article);

        //Enregistrer la panier dans la base de données.
        $repository ->save($basket , true);

        //Rediriger vers la page d'affichage du panier créé plus bas.
        return $this->redirectToRoute('app_basket_display');
    }

    #[Route('/mon-panier/{id}/plus', name: 'app_basket_plus')]
    public function plus(Article $article, ArticleRepository $repository): Response
    {
        //mettre la quantité à +1
        $quantity= $article->getQuantity();
        $article->setQuantity($quantity+1);
        //sauvegarde de la nouvelle quantité
        $repository->save($article, true);
        //redirection vers le panier
        return $this->redirectToRoute("app_basket_display");
    }

    #[Route('/mon-panier/{id}/moins', name: 'app_basket_moins')]
    public function minus(Article $article, ArticleRepository $repository, BasketRepository $basketRepo): Response
    {
         //mettre la quantité à -1
         $quantity= $article->getQuantity();
         $article->setQuantity($quantity-1);

         //test si la quatité est à 0
         if ($article->getQuantity() <= 0){
            //Pour supprimmer l'article du panier:

            //1. recuperer l'utilisateur puis son panier
            $user= $this->getUser();
            $basket= $user->getBasket();

            //2. supprimer de l'entité basket l'article
            $basket->removeArticle($article);

            //mettre à jour la bd via le besketRepo
            $basketRepo->save($basket, true);

            //redirection vers le panier
            return $this->redirectToRoute("app_basket_display");

         }


         //sauvegarde de la nouvelle quantité
         $repository->save($article, true);

         //redirection vers le panier
        return $this->redirectToRoute("app_basket_display");

    }

    #[Route('/mon-panier/{id}/supprimer', name: 'app_basket_remove')]
    public function remove(Article $article, BasketRepository $repository): Response
    {
        //Récupérer le panier de l'utilisateur connécté et supprimer les Articles (eg: `$basket->removeArticle($article)`).
        
        //récupérer l'utilisateur
        $user=$this->getUser();
        //récuprer la panier de l'utilisateur connecté
        $basket = $user->getBasket();
        //supprimer l'article du panier
        $basket->removeArticle($article);
        //Enregistré le panier dans la base de données.
        $repository->save($basket , true);
        //Rediriger vers la page d'affichage du panier.
        return $this->redirectToRoute('app_basket_display');
    }

    #[Route('/mon-panier/validation', name: 'app_basket_validate')]
    public function validate(Request $request, BasketRepository $repository, UserRepository $userrepo, OrderRepository $orderrepo): Response
    {

        //Création du paiment
        $payment= new Payment();

        //Récuperation de l'utilisateur connecté
        $user= $this->getUser();
      

        //création du form de paiement
        $form = $this->createForm(PaymentType::class , $payment);

        // remplissage du form
        $form->handleRequest($request);

        //si le formulaire est envoyé et est valide
        if($form->isSubmitted() && $form->isValid()){

            //créer la commande
            $order= new Order();

            // attacher la commande à l'utilisateur
            $order->setUser($user);

            // pour chaque livre du panier
            foreach($user->getBasket()->getArticles() as $article){
                //ajoute le livre à la commande
                $order->addArticle($article);

                //supprimer le livre du panier
                $user->getBasket()->removeArticle($article);
            }


         

            //sauvegarde des données
            $userrepo->save($user);
            $orderrepo->save($order);
            $repository->save($user->getBasket(), true);

            //redirection vers une page de détail
            // return new Response('Commande reçu avec succés');
            return $this->redirectToRoute('app_basket_display');
        }
        //affichage de la page de validation du panier
        return $this->render('/basket/validate.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
