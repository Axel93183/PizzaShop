<?php

namespace App\Controller;

use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route('/inscription', name: 'app_user_registration')]
    public function registration(UserRepository $repository, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        //création du formulaire
        $form= $this->createForm(RegistrationType::class);

        //remplissage du formulaire et de l'objet php avec la requete
        $form->handleRequest($request); 

        //si le formulaire est envoyé et les données sont valides
        if($form->isSubmitted() && $form->isValid()){

            //recuperation des données du formulaire dans un objet book
            $user = $form->getData();

            //crypter le mot de passe
            $cryptedPass= $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($cryptedPass);

            //enregistrer les donnée dans la bd
            $repository->save($user, true);

            //redirection vers la page d'accueil
            return $this->redirectToRoute('app_pizza_home');
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    // #[Route('/mon-profil', name: 'app_user_myprofil')]
    // public function myprofil(UserRepository $repository): Response
    // {
    //     $user = $security->getUser();
    
    //     return $this->render('author/index.html.twig', [
    //         'user' => $user,
    //     ]);
    // }
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/mon-profil', name: 'app_user_myProfile')]
    public function myProfile(UserRepository $repository, Request $request): Response
    {
        //récuperation de l'utilisateur connecté
        $user= $this->getUser();

        //creer le formulaire
        $form= $this->createForm(ProfileType::class, $user);

        //remplissage du formulaire 
        $form->handleRequest($request);

        //test si form is valid and submitted
        if($form->isSubmitted() && $form->isValid()){
            //on enregistre les modifications dans la bd via le repo
            $repository->add($user, true);
        }

        //affichage de la page HTML
        return $this->render('security/myProfile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
