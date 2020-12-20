<?php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompleteRegisterController extends AbstractController
{
    /**
     * @Route("/profil/complete-inscription", name="register")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }
}
