<?php

namespace App\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/**
 * Class DefaultController
 * @package App\Controller\Front
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index", methods={"GET"})
     * @Security("not is_granted('ROLE_ASSOC') and not is_granted('ROLE_ADH')", statusCode=404, message="Veuillez terminer votre inscription")
     */
    public function index()
    {
        return $this->render('front/default/index.html.twig');
    }

    /**
     * @Route("/custom", name="default_custom", methods={"GET"})
     */
    public function custom()
    {
        return $this->render('front/default/index.html.twig');
    }

    /**
     * @Route("/how-it-works", name="default_how-it-works", methods={"GET"})
     */
    public function how_it_works()
    {
        return $this->render('front/default/how-it-works.html.twig');
    }
}
