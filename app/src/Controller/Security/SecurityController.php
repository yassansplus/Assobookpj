<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\ForgetPassType;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $em;
    private $password;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->password = $passwordEncoder;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_default_index');
        } elseif ($this->getUser() && (in_array('ROLE_ADH',$this->getUser()->getRoles()) || in_array('ROLE_ASSOC',$this->getUser()->getRoles()))){
            return $this->redirectToRoute('profile_register');
        } elseif($this->getUser() && (!in_array('ROLE_ADH',$this->getUser()->getRoles()) || !in_array('ROLE_ASSOC',$this->getUser()->getRoles()))){
            return $this->redirectToRoute('default_connect');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        $this->redirectToRoute('default_index');
    }

    /**
     * @Route("/mot-de-passe-oublie", name="forgetpwd")
     */
    public function forgetPassword(Request $request, UserRepository $users, EmailService $emailService, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->createForm(ForgetPassType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $donnees = $form->getData();

            // On cherche un utilisateur ayant cet e-mail
            $user = $users->findOneBy(['email' => strtolower($donnees->getEmail())]);

            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                $this->addFlash('danger', 'Cette adresse e-mail est inconnue');

                // On retourne sur la page de connexion
                return $this->redirectToRoute('forgetpwd');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            // On essaie d'écrire le token en base de données
            try{
                $user->setResetToken($token);
                $this->em->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('resetpwd', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            // On utilise le service EmailService s'occupant de créer l'email et de l'envoyer
            $emailService->sendMail('Mot de passe oublié',$user->getEmail(),[$this->renderView(
                'emails/reset_password.html.twig', ['url' => $url]),
                'text/html']);

            // On crée le message flash de confirmation
            $this->addFlash('success', 'E-mail de réinitialisation du mot de passe envoyé !');

            // On redirige vers la page de login
            return $this->redirectToRoute('app_login');
        }

        // On envoie le formulaire à la vue
        return $this->render('security/reset_password.html.twig',['form' => $form->createView()]);
    }

    /**
     * @Route("/reintialisation-mot-de-passe/{token}", name="resetpwd")
     */
    public function resetPassword(Request $request, string $token)
    {
        // On cherche un utilisateur avec le token donné
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();
            // On supprime le token
            $user->setResetToken(null);
            $user->setPassword($this->password->encodePassword($user,$donnees->getPassword()));
            $this->em->flush();
            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('app_login');
        }else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView(), 'token' => $token
            ]);
        }
    }
}