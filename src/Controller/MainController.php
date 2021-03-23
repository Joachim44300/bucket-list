<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/home", name="main_home")
     */
    public function home(): Response
    {
        return $this->render('main/home.html.twig', [

        ]);
    }

    /**
     * @Route("/aboutUs", name="main_aboutUs")
     */
    public function aboutUs(): Response
    {
        return $this->render('main/about_us.html.twig', [

        ]);
    }

}