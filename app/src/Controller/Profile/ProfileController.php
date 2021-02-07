<?php

namespace App\Controller\Profile;

use App\Entity\Adherent;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController {
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    protected function getArrayMessage($title, $data=null){
        $array = [
            'title' => $title,
        ];
        if(!is_null($data)){
            $array['data'] = $data;
        }
        return $array;
    }

    /**
     * @Route("/mon-compte", name="account")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $hasRole = $this->isGranted('ROLE_ADH');
        return $this->render('profile/account.html.twig');
    }

    /**
     * @Route("/edit-nom", name="edit_name")
     */
    public function editName(Request $request): Response
    {
        if($request->isXmlHttpRequest()){
            $name = $request->request->get('name');
            $currentUser = $this->getUser();
            $adherent = $this->em->getRepository(Adherent::class)->findById($currentUser->getAdherent())[0];
            if($adherent->getFirstname() === $name){
                return new JsonResponse($this->getArrayMessage('Warning'));
            }
            if(empty($name)){
                return new JsonResponse($this->getArrayMessage('Error',$adherent->getFirstname()));
            }
            $adherent->setFirstname($name);
            $this->em->flush();
            return new JsonResponse($this->getArrayMessage('OK'));
        }
        return new JsonResponse('Une erreur est survenue',500);
    }
}