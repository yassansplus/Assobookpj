<?php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController {
    /**
     * @Route("/mon-compte", name="account")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $hasRole = $this->isGranted('ROLE_ADH');
        return $this->render('profile/account.html.twig');
    }
}