<?php

namespace App\Controller\Connect;

use App\Entity\Adherent;
use App\Entity\Association;
use App\Entity\Event;
use App\Entity\Publication;
use App\Form\CommentaireType;
use App\Entity\Commentaire;
use App\Form\PublicationType;
use App\Form\EventType;
use Faker\Provider\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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
        $repositoryEvent = $this->getDoctrine()->getRepository(Event::class);

        if ($this->isGranted('ROLE_ADH_CONFIRME')) {
            $data = $this->getUser()->getAdherent()->getAssociations();
            $events = $repositoryEvent->findAll();
            foreach ($data as $assoc) {
                foreach ($assoc->getPublications() as $publication) {
                    $publications[] = $publication;
                }
            }
        }

        if ($this->isGranted('ROLE_ASSOC_CONFIRME')) {
            $formPublication = new Publication();
            $event = new Event();
            $form = $this->createForm(PublicationType::class, $formPublication);
            $formEvent = $this->createForm(EventType::class, $event);
            $form->handleRequest($request);
            $formEvent->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $formPublication->setAssociation($this->getUser()->getAssociation());
                $formPublication->setDatePublication();
                $entityManager->persist($formPublication);
                $entityManager->flush();
            }

            if ($formEvent->isSubmitted() && $formEvent->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $event->getPublication()->setAssociation($this->getUser()->getAssociation());
                $event->getPublication()->setDatePublication();
                $event->setAssociation($this->getUser()->getAssociation());
                $entityManager->persist($event);
                $entityManager->flush();
            }

            $id = $this->getUser()->getAssociation()->getId();
            $repository = $this->getDoctrine()->getRepository(Publication::class);
            $assocPublications = $repository->findBy(
                ['association' => $id]
            );

            $events = $repositoryEvent->findBy(
                ['association' => $id]
            );

            return $this->render('front/connected/index.html.twig',
                [
                    'publications' => $publications,
                    'formPublication' => $form->createView(),
                    'formEvent' => $formEvent->createView(),
                    'assocPublications' => $assocPublications,
                    'events' => $events
                ]);
        } else {
            return $this->render('front/connected/index.html.twig',
                [
                    'publications' => $publications,
                    'events' => $events
                ]);
        }
    }


    /**
     * @Route("/association/add/comment/{id}", name="add_comment")
     */
    public function addCommentaire(Publication $publication, Request $request): Response
    {

        //header("Content-Type: application/json");
        try {
            //$json_str = file_get_contents('php://input');
            //$json_obj = json_decode($json_str);
            $comment = new Commentaire();
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setUserId($this->getUser());
            $comment->setCreatedAt();
            $comment->setPublicationId($publication);
            $comment->setContent($request->request->get('commentaire'));
            $entityManager->persist($comment);
            $entityManager->flush();

            /*
            $commentaire = [
                "id_user" => $this->getUser()->getAssociation() ? $this->getUser()->getAssociation()->getId() : $this->getUser()->getAdherent()->getId(),
                "contenu" => $json_obj->commentaire,
                "date" => $comment->getCreatedAt()->format("d/m/Y"),
                "heure" =>$comment->getCreatedAt()->format("H:i"),
                "is_association" => $this->getUser()->getAssociation() ? true : false,
                "fullname" => $this->getUser()->getAssociation() ? $this->getUser()->getAssociation()->getName() : $this->getUser()->getAdherent()->getFirstname(). ' ' .  $this->getUser()->getAdherent()->getLastname()
            ];
            */

            return new JsonResponse("Votre commentaire a bien été ajouté !");
        } catch (Exception $e) {
            return new JsonResponse("une erreur est survenu:" . $e->getMessage());
        }


    }
}
