<?php

namespace App\Controller\Profile;

use App\Entity\Adherent;
use App\Entity\Association;
use App\Form\AdherentType;
use App\Form\AssociationType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class CompleteRegisterController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/profil/complete-inscription", name="register")
     */
    public function index(Request $request, AppCustomAuthenticator $login, GuardAuthenticatorHandler $guard): Response
    {
        $user = $this->getUser();
        $hasRole = $this->isGranted('ROLE_ADH');

        $typeUser = $hasRole ? new Adherent() : new Association();
        $form = $this->createForm($hasRole ? AdherentType::class : AssociationType::class, $typeUser);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $adherent = $form->getData();
            if(!$hasRole){
                $dataAddress = $form->get('adress')->getData();
                $this->em->persist($dataAddress);
                $this->em->flush();
            }

            $adherent->setUserAccount($this->getUser());
            !$hasRole ? $adherent->setAddress($dataAddress) : '';
            $user->setRoles([$hasRole ? 'ROLE_ADH_CONFIRME' : 'ROLE_ASSOC_CONFIRME']);
            $this->em->persist($typeUser);
            $this->em->flush();
            return $guard->authenticateUserAndHandleSuccess($user, $request, $login,'main');
        }
        return $this->render('profile/index.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
