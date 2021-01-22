<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    /**
     * @Route("/s-inscrire", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        if ($this->getUser() && (in_array('ROLE_ADH',$this->getUser()->getRoles()) || in_array('ROLE_ASSOC',$this->getUser()->getRoles()))) {
            return $this->redirectToRoute('profile_register');
        }elseif($this->getUser()){
            return $this->redirectToRoute('default_index');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $user->setEmail(strtolower($user->getEmail()));
            $passwordEncoder = $passwordEncoder->encodePassword($user,$user->getPassword());
            $user->setPassword($passwordEncoder);

            $user->setToken(md5(uniqid()));
            $this->em->persist($user);
            $this->em->flush();

            $message = (new \Swift_Message('Nouveau compte'))
                ->setFrom('assobookpa@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig', ['token' => $user->getToken()]
                    ),
                    'text/html'
                );

            $mailer->send($message);
            $this->addFlash('success','Inscription effectué. Un email de confirmation vous a été envoyé');

            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $users)
    {
        $user = $users->findOneBy(['token' => $token]);

        if(!$user){
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $user->setToken(null);
        // On met le champs confirm a False
        $this->em->flush();

        $this->addFlash('success', 'Votre compte a été activé avec succès');

        return $this->redirectToRoute('app_login');
    }
}
