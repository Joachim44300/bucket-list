<?php

namespace App\Controller;

use App\Entity\Reaction;
use App\Entity\Wish;
use App\Form\ReactionType;
use App\Form\WishType;
use App\Repository\ReactionRepository;
use App\Repository\WishRepository;
use App\Util\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishController extends AbstractController
{
    /**
     * @Route("/contribuer/", name="wish_new")
     */
    public function newWish(Request $request, EntityManagerInterface $entityManager, Censurator $censurator): Response
    {
        // Crée une instance de l'entité que le form sert à créer
        $wish = new Wish();

        // Remplissage du pseudo dans le formulaire
         $currentUsername = $this->getUser()->getUsername();
         $wish->setAuthor($currentUsername);

        // Crée une instance de la classe de formulaire
        // On associe cette entité à notre formulaire
        $wishForm = $this->createForm(WishType::class, $wish);

        // On prend les données du formulaire soumis, et les injecte dans mon $wish
        $wishForm->handleRequest($request);

        // Si le formulaire est soumis
        if ($wishForm->isSubmitted() && $wishForm->isValid()) {

            // Hydrate les propriétés qui sont encore null
            $wish->setIsPublished(true);
            $wish->setDateCreated(new \DateTime());

            // On appelle le service Censurator
            $censuratorDescription = $censurator->purify($wish->getDescription());
            $wish->setDescription($censuratorDescription);

            // Sauvegarde en Bdd
            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash("success", "Le souhait a été enregistré");

            // Redirige vers une autre page, ou vers la page actuelle pour vider le form
            return $this->redirectToRoute("wish_detail", [
                'id' => $wish->getId()
            ]);

        }

        return $this->render("/wishes/new_wish.html.twig", [
            // Passe l'instance à twig pour affichage
            "wishForm" => $wishForm->createView()
        ]);
    }

    /**
     * @Route("/wishes/{page}", name="wish_list", requirements={"page": "\d+"})
     */
    public function list(WishRepository $wishRepository, int $page = 1): Response
    {
        //$wishes = $wishRepository->findBy([], ['dateCreated' => "DESC"], 30, 0);
        //todo: requête à la bdd pour aller chercher tous les wishes
        $result = $wishRepository->findWishList($page);
        $wishes = $result['result'];

        return $this->render('wish/list.html.twig', [
            "wishes" => $wishes,
            "totalResultCount" => $result['totalResultCount'],
            "currentPage" => $page,
        ]);
    }

    /**
     * @Route("/wishes/detail/{id}", name="wish_detail")
     */
    public function detail($id, WishRepository $wishRepository, ReactionRepository $reactionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $wish = $wishRepository->find($id);
        $allReaction = $reactionRepository->findBy(['wish' => $wish]);

        // Crée une instance de l'entité que le form sert à créer
        $reaction = new Reaction();

        // Crée une instance de la classe de formulaire
        // On associe cette entité à notre formulaire
        $reactionForm = $this->createForm(ReactionType::class, $reaction);

        // On prend les données du formulaire soumis, et les injecte dans mon $wish
        $reactionForm->handleRequest($request);

        // Si le formulaire est soumis
        if ($reactionForm->isSubmitted() && $reactionForm->isValid()) {
            // Hydrate les propriétés qui sont encore null
            $reaction->setDateCreated(new \DateTime());
            $reaction->setWish($wish);

            // Sauvegarde en Bdd
            $entityManager->persist($reaction);
            $entityManager->flush();

            $this->addFlash("success", "Le message a été enregistré");

            // Redirige vers une autre page, ou vers la page actuelle pour vider le form
            return $this->redirectToRoute("wish_detail", [
                'id' => $wish->getId()
            ]);
        }

        return $this->render('wish/detail.html.twig', [
            "wish" => $wish,
            "reactions" => $allReaction,
            "reactionForm" => $reactionForm->createView()
        ]);
    }

    /**
     * @Route("/wishes/test/bdd", name="wish_bdd")
     */
    public function show(EntityManagerInterface $entityManager): Response
    {
        // Instanciation d'un nouveau souhait
        $wish = new Wish();

        // Hydrate toutes les propriétés
        $wish->setTitle('Saut à l élastique');
        $wish->setDescription('Un saut à l elastique');
        $wish->setAuthor('Joachim');
        $wish->setIsPublished(true);
        $wish->setDateCreated(new \DateTime());

        $entityManager->persist($wish);
        $entityManager->flush();

        die("ok");
    }

}
