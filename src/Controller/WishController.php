<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishController extends AbstractController
{
    /**
     * @Route("/wishes", name="wish_list")
     */
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy([], ['dateCreated' => "DESC"], 30, 0);

        return $this->render('wish/list.html.twig', [
            "wishes" => $wishes
        ]);
    }

    /**
     * @Route("/wishes/{id}", name="wish_detail")
     */
    public function detail($id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);
        return $this->render('wish/detail.html.twig', [
            "wishes" => $wish
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
