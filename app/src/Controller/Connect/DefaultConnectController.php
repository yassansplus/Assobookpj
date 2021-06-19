<?php

namespace App\Controller\Connect;

use App\Entity\Publication;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DefaultConnectController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/home", name="default_connect")
     * @Security("is_granted('ROLE_ADH_CONFIRME') or is_granted('ROLE_ASSOC_CONFIRME')", statusCode=403, message="Veuillez vous connecter")
     */
    public function index(): Response
    {
        $publications = $this->em->getRepository(Publication::class)->findAll();
        //$count = count($publications);
        return $this->render('front/connected/index.html.twig',
            [
                'publications' => $publications,
                //'count' => $count,
            ]);
    }

    /**
      @Route("/publication", name="create_publication")

    public function createPublication(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $publication = new Publication();
        $publication->setDescription('Ergonomic and stylish!');
        $publication->setDatePublication();
        $entityManager->persist($publication);
        $entityManager->flush();
        return new Response('Saved new product with id '.$publication->getId());
    }
     * */


}
