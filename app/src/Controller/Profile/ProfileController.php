<?php

namespace App\Controller\Profile;

use App\Entity\Adherent;
use App\Entity\Association;
use App\Form\UpdatePwdType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController {
    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder){
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function getArrayMessage($title, $typeUser, $data = null)
    {
        $array = [
            'title' => $title,
            'type' => $typeUser,
        ];
        if (!is_null($data)) {
            $array['data'] = $data;
        }
        return $array;
    }

    /**
     * @Route("/mon-compte", name="account")
     */
    public function index(): Response
    {
        $form = $this->createForm(UpdatePwdType::class);
        return $this->render('profile/account.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit-nom", name="edit_name")
     */
    public function editName(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $name = $request->request->get('name');
            $typeAjax = $request->request->get('type');
            $currentUser = $this->getUser();
            $getType = $currentUser->getAdherent();
            //0 : adherent / 1: association
            $type = $getType ? 0 : 1;
            $typeUser = $this->em->getRepository($getType ? Adherent::class : Association::class)->findById($getType ? $getType : $currentUser->getAssociation())[0];

            if ($getType) $firstnameOrLastname = $typeAjax === 'adh-lastname' ? $typeUser->getLastname() : $typeUser->getFirstname();
            $dataCompare = $getType ? $firstnameOrLastname : $typeUser->getName();

            if ($dataCompare === $name) return new JsonResponse($this->getArrayMessage('Warning', $type));

            if (empty($name)) return new JsonResponse($this->getArrayMessage('Error', $type, $dataCompare));
            if ($getType) $setFirstnameOrLastname = $typeAjax === 'adh-lastname' ? $typeUser->setLastname($name) : $typeUser->setFirstname($name);
            $getType ? $setFirstnameOrLastname : $typeUser->setName($name);
            $this->em->flush();

            if ($getType) $firstnameOrLastname = $typeAjax === 'adh-lastname' ? $typeUser->getLastname() : $typeUser->getFirstname();
            $dataCompare = $getType ? $firstnameOrLastname : $typeUser->getName();
            return new JsonResponse($this->getArrayMessage('OK', $type, $dataCompare));
        }
        return new JsonResponse('Une erreur est survenue', 500);
    }

    /**
     * @Route("/reset-password", name="reset_password")
     */
    public function resetPassword(Request $request)
    {
        if($request->isXmlHttpRequest()){
            $form = $request->request->all();
            $currentUser = $this->getUser();
            $type = $currentUser->getAdherent() ? 0 : 1;

            if((int)$form['input'] !== 3 || (int)$form['required'] !== 3){
                return new JsonResponse('Input');
            }
            if(!$this->passwordEncoder->isPasswordValid($currentUser, $form['oldpwd'])){
                return new JsonResponse('Pwd');
            }
            if($form['newpwd'] !== $form['confirmpwd']){
                return new JsonResponse('Equals');
            }
            if(strlen($form['newpwd']) < 8 || strlen($form['newpwd']) > 16){
                return new JsonResponse('Size');
            }
            $currentUser->setPassword($this->passwordEncoder->encodePassword($currentUser,$form['newpwd']));
            $this->em->flush();
            return new JsonResponse('OK');
        }
        return new JsonResponse('Error',500);
    }


    /**
     * @Route("/follow", name="follow")
     */
    public function follow(Request $request): Response
    {
        $association = $request->get('association');
        $em = $this->getDoctrine()->getManager();
        $association = $em->getRepository(Association::class)->find($association);
        $adherent = $this->getUser()->getAdherent();
        if (!in_array($this->getUser()->getAdherent(), $association->getAdherents()->toArray())) {
            $association->addAdherent($adherent);
            $em->flush();
            return new JsonResponse(['title' => 'ET BOOM!💥', 'message' => 'Vous suivez desormais ' . $association->getName()]);
        } else {
            $association->removeAdherent($adherent);
            $em->flush();
            return new JsonResponse(['title' => 'ET BOOM!💥', 'message' => 'Vous ne suivez plus ' . $association->getName()]);
        }
    }

}
