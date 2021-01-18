<?php

namespace App\Controller\Profile;

use App\Entity\Adherent;
use App\Entity\Association;
use App\Form\AdherentType;
use App\Form\AssociationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if($this->isGranted('ROLE_ADH')){
            $adherent = new Adherent();
            $form = $this->createForm(AdherentType::class, $adherent);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $adherent = $form->getData();
                $adherent->setUserAccount($this->getUser());

                $user->setRoles(['ROLE_ADH_CONFIRME']);
                $this->em->persist($adherent);
                $this->em->flush();

                return $this->redirectToRoute('default_index');
            }
        }elseif($this->isGranted('ROLE_ASSOC')){
            $assoc = new Association();
            $form = $this->createForm(AssociationType::class, $assoc);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $assoc = $form->getData();
                $assoc->setUserAccount($this->getUser());

                $user->setRoles(['ROLE_ASSOC_CONFIRME']);
                $this->em->persist($assoc);
                $this->em->flush();

                return $this->redirectToRoute('default_index');
            }
        }
        return $this->render('profile/index.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
