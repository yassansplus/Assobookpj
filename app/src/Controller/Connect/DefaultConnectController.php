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
use App\Entity\EventAdherent;
use App\Form\EventAdherentType;
use App\Repository\EventAdherentRepository;
use Symfony\Component\Validator\Constraints\Date;

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

            $eventAdherent = new EventAdherent;
            $eventAdherentForm = $this->createForm(EventAdherentType::class, $eventAdherent);
            $eventAdherentForm->handleRequest($request);
        }

        if ($this->isGranted('ROLE_ASSOC_CONFIRME')) {
            $formPublication = new Publication();
            $event = new Event();
            $dateRef = new \DateTime;
            $newDate = $dateRef->format('Y-m-d H:i:s');
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
                $dateStart = $event->getStartDate();
                $newDateStart = $dateStart->format('Y-m-d H:i:s');
                $dateEnd = $event->getEndingDate();
                $newDateEnd = $dateEnd->format('Y-m-d H:i:s');
                if ($newDate <= $newDateStart && $newDateStart <= $newDateEnd){
                    $entityManager = $this->getDoctrine()->getManager();
                    $event->getPublication()->setAssociation($this->getUser()->getAssociation());
                    $event->getPublication()->setDatePublication();
                    $event->setAssociation($this->getUser()->getAssociation());
                    $entityManager->persist($event);
                    $entityManager->flush();
                    $this->addFlash('success','L\'évènement a bien été crée');
                } else {
                    $this->addFlash('danger','Veuillez vérifier vos dates !');
                }
            }

            $id = $this->getUser()->getAssociation()->getId();
            $repository = $this->getDoctrine()->getRepository(Publication::class);
            $assocPublications = $repository->findBy(
                ['association' => $id]
            );

            $events = $repositoryEvent->findBy(
                ['association' => $id]
            );

            if (($form->isSubmitted() && $form->isValid()) || ($formEvent->isSubmitted() && $formEvent->isValid())){
                return $this->redirectToRoute('default_connect');
            }

            return $this->render('front/connected/index.html.twig',
                [
                    'publications' => $publications,
                    'formPublication' => $form->createView(),
                    'formEvent' => $formEvent->createView(),
                    'assocPublications' => $assocPublications,
                    'events' => $events,
                ]);
        } else {
            return $this->render('front/connected/index.html.twig',
                [
                    'publications' => $publications,
                    'events' => $events,
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
            $comment->setContent($request->request->get('commentaire'));
            $comment->setPublicationId($publication);
            $entityManager->persist($comment);
            $entityManager->flush();

            return new JsonResponse("Votre commentaire a bien été ajouté !");
        } catch (Exception $e) {
            return new JsonResponse("une erreur est survenu:" . $e->getMessage());
        }
    }

    /**
     * @Route("/participate", name="add_participate")
     */
    public function participation(Request $request): Response{
        try{
            if($this->getUser()->getAdherent()){
                $event = $this->em->getRepository(Event::class)->find((int) $request->request->get('event'));
                $adherent = $this->getUser()->getAdherent();
                $eventAdh = new EventAdherent();
                $eventAdh->setAdherent($adherent)
                    ->setEvent($event);
                $this->em->persist($eventAdh);
                $this->em->flush();
                return new JsonResponse(200);
            }else{
                return new JsonResponse(403);
            }
        }catch (Exception $e) {
            return new JsonResponse("une erreur est survenu:" . $e->getMessage());
        }
    }

    /**
     * @Route("/desinscrire", name="desinscrire_participate")
     */
    public function desinscrire(Request $request): Response{
        try{
            if($this->getUser()->getAdherent()){
                $event = $this->em->getRepository(Event::class)->find((int) $request->request->get('event'));
                $eventAdh = $this->em->getRepository(EventAdherent::class)->findBy(["event" => $event,"adherent" => $this->getUser()->getAdherent()])[0];
                $this->em->remove($eventAdh);
                $this->em->flush();
                return new JsonResponse(204);
            }else{
                return new JsonResponse(403);
            }
        }catch (Exception $e) {
            return new JsonResponse("une erreur est survenu:" . $e->getMessage());
        }
    }
}
