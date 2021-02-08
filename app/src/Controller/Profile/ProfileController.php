<?php

namespace App\Controller\Profile;

use App\Entity\Adherent;
use App\Entity\Association;
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

    protected function getArrayMessage($title, $typeUser, $data=null){
        $array = [
            'title' => $title,
            'type' => $typeUser,
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
        return $this->render('profile/account.html.twig');
    }

    /**
     * @Route("/edit-nom", name="edit_name")
     */
    public function editName(Request $request): Response
    {
        if($request->isXmlHttpRequest()){
            $name = $request->request->get('name');
            $typeAjax = $request->request->get('type');
            $currentUser = $this->getUser();
            $getType = $currentUser->getAdherent();
            //0 : adherent / 1: association
            $type = $getType ? 0 : 1;
            $typeUser = $this->em->getRepository($getType ? Adherent::class : Association::class)->findById($getType ? $getType : $currentUser->getAssociation())[0];

            if($getType) $firstnameOrLastname = $typeAjax === 'adh-lastname' ? $typeUser->getLastname() : $typeUser->getFirstname();
            $dataCompare = $getType ? $firstnameOrLastname : $typeUser->getName();

            if($dataCompare === $name) return new JsonResponse($this->getArrayMessage('Warning', $type));

            if(empty($name)) return new JsonResponse($this->getArrayMessage('Error', $type, $dataCompare));
            if($getType) $setFirstnameOrLastname = $typeAjax === 'adh-lastname' ? $typeUser->setLastname($name) : $typeUser->setFirstname($name);
            $getType ? $setFirstnameOrLastname : $typeUser->setName($name);
            $this->em->flush();

            if($getType) $firstnameOrLastname = $typeAjax === 'adh-lastname' ? $typeUser->getLastname() : $typeUser->getFirstname();
            $dataCompare = $getType ?  $firstnameOrLastname : $typeUser->getName();
            return new JsonResponse($this->getArrayMessage('OK', $type, $dataCompare));
        }
        return new JsonResponse('Une erreur est survenue',500);
    }
}