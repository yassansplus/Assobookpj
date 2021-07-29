<?php

namespace App\Controller\Cart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Association;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CartController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/carte", name="cart")
     * * @Security("is_granted('ROLE_ADH_CONFIRME')", statusCode=403, message="Cette page est réservé aux adhérents")
     */
    public function index(): Response
    {
        //$count = '';
        $associations = $this->em->getRepository(Association::class)->findAll();
        $count = count($associations);
        return $this->render('cart/index.html.twig', [
            'associations' => $associations,
            'count' => $count
        ]);
    }
}
