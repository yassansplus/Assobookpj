<?php

namespace App\Controller\Front;

use App\Form\ContactType;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller\Front
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index", methods={"GET"})
     * @return Response
     */
    public function index()
    {
        if($this->getUser()){
            return $this->redirectToRoute("default_connect");
        }
        $form = $this->createForm(ContactType::class); // Formulaire de contact
        return $this->render('front/default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/custom", name="default_custom", methods={"GET"})
     */
    public function custom()
    {
        if($this->getUser()){
            return $this->redirectToRoute("default_connect");
        }
        return $this->render('front/default/index.html.twig');
    }

    /**
     * @Route("/how-it-works", name="default_how-it-works", methods={"GET"})
     */
    public function how_it_works()
    {
        if($this->getUser()){
            return $this->redirectToRoute("default_connect");
        }
        return $this->render('front/default/how-it-works.html.twig');
    }

    /**
     * @Route("/who-are-we", name="default_who_are_we", methods={"GET"})
     */
    public function who_are_we()
    {
        if($this->getUser()){
            return $this->redirectToRoute("default_connect");
        }
        return $this->render('front/default/who-are-we.html.twig');
    }

    /**
     * @param Request $request
     * @param EmailService $emailService
     * @Route("/contact-form", name="default_contact-form", methods={"POST"});
     */
    public function contact_form(Request $request, EmailService $emailService){
        if($this->getUser()){
            return $this->redirectToRoute("default_connect");
        }
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();
            $emailService->sendMail('Assobook - Nouveau Message', 'assobookpa@gmail.com',
                [$this->renderView('emails/contact-form.html.twig',
                    ['nom' => $formData['nom'],
                        'prenom' => $formData['prenom'],
                        'message' => $formData['message'],
                        'email' => $formData['email']
                    ]), 'text/html']);
        }
        return $this->redirectToRoute('default_index');
    }
}
