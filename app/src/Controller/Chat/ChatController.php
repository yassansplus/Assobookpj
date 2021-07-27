<?php

namespace App\Controller\Chat;

use App\Entity\Adherent;
use App\Entity\Association;
use App\Entity\Chat;
use App\Entity\Conversation;
use App\Entity\User;
use App\Form\ChatType;
use App\Repository\ChatRepository;
use App\Repository\ConversationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chat")
 * @package App\Controller\Chat
 * @Security("is_granted('ROLE_ADH_CONFIRME') or is_granted('ROLE_ASSOC_CONFIRME')", statusCode=403, message="Veuillez vous connecter")
 */
class ChatController extends AbstractController
{
    /**
     * @Route("/message-to/{id}", name="chat_To", methods={"GET"})
     */
    public function messageTo(ChatRepository $chatRepository, $id): Response
    {
        return $this->render('chat/new.html.twig', $this->getMessage($chatRepository, $id));

    }

    /**
     * @Route("/updateMessage/{id}", name="updateMessage", methods={"GET"})
     */
    public function updateMessage(ChatRepository $chatRepository, $id): Response
    {
        return $this->render('chat/chatLoop.html.twig', $this->getMessage($chatRepository, $id));

    }




    /**
     * @Route("/new", name="chat_new", methods={"POST"})
     */
    public function new(Request $request, ChatRepository $chatRepository): Response
    {

        $chat = new Chat();
        $em = $this->getDoctrine();
        $isLoggedAsAdherent = in_array("ROLE_ADH_CONFIRME", $this->getUser()->getRoles());
        if ($isLoggedAsAdherent) {
            $adherent = $this->getUser()->getAdherent();
            $association = $em->getRepository(Association::class)->find($request->request->get('userToTalk'));
            $id = $association;
            $conversation = $this->getDoctrine()
                ->getRepository(Conversation::class)
                ->findOneBy(['association' => $association,
                    'adherent' => $adherent]);
            $chat->setSentBy($adherent->getUserAccount());

        } else {
            $association = $this->getUser()->getAssociation();
            $adherent = $em->getRepository(Association::class)->find($request->request->get('userToTalk'));
            $id = $adherent;
            $conversation = $this->getDoctrine()
                ->getRepository(Conversation::class)
                ->findOneBy(['association' => $association,
                    'adherent' => $adherent]);
            $chat->setSentBy($association->getUserAccount());
        }

        if ($conversation == null) {
            $conversation = new Conversation();
            $conversation->setAdherent($adherent);
            $conversation->setAssociation($association);
        }
        $chat->setMessage($request->request->get('message'));
        $conversation->addMessage($chat);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($conversation);
        $entityManager->flush();

        return $this->render('chat/chatLoop.html.twig', $this->getMessage($chatRepository, $id));
    }

    private function getMessage(ChatRepository $chatRepository, $id)
    {


        if (in_array("ROLE_ADH_CONFIRME", $this->getUser()->getRoles())) {
            $userToTalk = $this->getDoctrine()->getRepository(Association::class)->find($id);
            $chat = $this->getDoctrine()->getRepository(Conversation::class)
                ->findOneBy(['association' => $id, 'adherent' => $this->getUser()->getAdherent()]);
        } else {
            $userToTalk = $this->getDoctrine()->getRepository(Adherent::class)->find($id);
            $chat = $this->getDoctrine()->getRepository(Conversation::class)
                ->findOneBy(['adherent' => $id, 'association' => $this->getUser()->getAssociation()]);
        }
        return [
            'userToTalk' => $userToTalk,
            'conversation' => $chat,
        ];


    }
}
