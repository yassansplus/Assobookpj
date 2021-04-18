<?php

namespace App\Controller\Connect;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultConnectController extends AbstractController
{
    /**
     * @Route("/home", name="default_connect")
     * @Security("is_granted('ROLE_ADH_CONFIME') or is_granted('ROLE_ASSOC_CONFIRME')", statusCode=403, message="Veuillez vous connecter")
     */
    public function index(): Response
    {
        return $this->render('front/connected/index.html.twig');
    }
}
