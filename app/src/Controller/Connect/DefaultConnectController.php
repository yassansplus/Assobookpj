<?php

namespace App\Controller\Connect;

use App\Entity\Adherent;
use App\Entity\Association;
use App\Entity\Publication;
use App\Form\CommentaireType;
use App\Entity\Commentaire;
use App\Form\PublicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ProfileController
 * @package App\Controller\Connect
 * @Security("is_granted('ROLE_ADH_CONFIRME') or is_granted('ROLE_ASSOC_CONFIRME')", statusCode=403, message="Veuillez vous connecter")
 */
class DefaultConnectController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/home", name="default_connect")
     */
    public function index(Request $request): Response
    {
        $publications = [];
        $comment = new Commentaire;
        $commentForm = $this->createForm(CommentaireType::class, $comment);
        $commentForm->handleRequest($request);

        if ($this->isGranted('ROLE_ADH_CONFIRME')) {
            $data = $this->getUser()->getAdherent()->getAssociations();
            foreach ($data as $assoc) {
                foreach ($assoc->getPublications() as $publication) {
                    $publications[] = $publication;
                }
            }
        }

        if ($this->isGranted('ROLE_ASSOC_CONFIRME')) {
            $formPublication = new Publication();
            $form = $this->createForm(PublicationType::class, $formPublication);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $formPublication->setAssociation($this->getUser()->getAssociation());
                $formPublication->setDatePublication();
                $entityManager->persist($formPublication);
                $entityManager->flush();
            }

            $id = $this->getUser()->getAssociation()->getId();
            $repository = $this->getDoctrine()->getRepository(Publication::class);
            $assocPublications = $repository->findBy(
                ['association' => $id]
            );

            return $this->render('front/connected/index.html.twig',
                [
                    'publications' => $publications,
                    'formPublication' => $form->createView(),
                    'assocPublications' => $assocPublications
                ]);
        } else {
            return $this->render('front/connected/index.html.twig',
                [
                    'publications' => $publications,
                ]);
        }
    }


    /**
     * @Route("/association/add/comment/{id}", name="add_comment")
     */
    public function addCommentaire(Publication $publication, Request $request): Response
    {
        try {
            $comment = new Commentaire();
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setUserId($this->getUser());
            $comment->setCreatedAt();
            $comment->setPublicationId($publication);
            $comment->setContent($request->request->get('commentaire'));
            $entityManager->persist($comment);
            $entityManager->flush();

            return new JsonResponse("Votre commentaire a bien été ajouté");
        } catch (Exception $e) {
            return new JsonResponse("une erreur est survenu:" . $e->getMessage());
        }

    }

    /**
     * @Route("/adherent/{id}", name="default_show_adherent")
     */
    public function showAdherent($id): Response
    {
        $adherent = $this->em->getRepository(Adherent::class)->find($id);
        $count = $this->countBestTheme($adherent->getAssociations());
        $allAssoc = $this->em->getRepository(Association::class)->findAll();
        shuffle($allAssoc);
        return $this->render('front/connected/index.html.twig', [
            'adherent' => $adherent,
            'allAssoc' => $allAssoc,
            'count' => $count
        ]);
    }
}
