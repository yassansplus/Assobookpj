<?php

namespace App\Controller\Profile;

use App\Entity\Adherent;
use App\Entity\Association;
use App\Entity\ThemeAssoc;
use App\Form\AddressType;
use App\Form\UpdateAdherentType;
use App\Form\UpdateAssocType;
use App\Form\UpdateUserType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Error;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileController
 * @package App\Controller\Profile
 * @Security("is_granted('ROLE_ADH_CONFIRME') or is_granted('ROLE_ASSOC_CONFIRME')", statusCode=403, message="Veuillez vous connecter")
 */
class ProfileController extends AbstractController
{
    private $em;
    private $password;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->password = $passwordEncoder;
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
     * @Route("/profil", name="account")
     */
    public function profile(): Response
    {
        $association = '';
        $count = '';
        if($this->getUser()->getAdherent()){
            $association = $this->em->getRepository(Association::class)->findAll();
            $count = $this->countBestTheme($this->getUser()->getAdherent()->getAssociations());
            shuffle($association);
        }
        return $this->render('profile/account.html.twig',[
            'allAssoc' => $association,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/association/{id}", name="show")
     */
    public function showAssociation($id): Response
    {
        $association = $this->em->getRepository(Association::class)->find($id);
        return $this->render('profile/account.html.twig',[
            'association' => $association
        ]);
    }

    /**
     * @Route("/adherent/{id}", name="show_adherent")
     */
    public function showAdherent($id): Response
    {
        $adherent = $this->em->getRepository(Adherent::class)->find($id);
        $count = $this->countBestTheme($adherent->getAssociations());
        $allAssoc = $this->em->getRepository(Association::class)->findAll();
        shuffle($allAssoc);
        return $this->render('profile/account.html.twig',[
            'adherent' => $adherent,
            'allAssoc' => $allAssoc,
            'count' => $count
        ]);
    }

    /**
     * @Route("/association/theme/{id}", name="list_association")
     */
    public function showAssociationByTheme($id, PaginationService $paginationService, Request $request){
        $users = $this->em->getRepository(ThemeAssoc::class)->find($id);
        foreach($users->getAssociations() as $user){
            $arrayUser[] = $user;
        }
        $pagination = $paginationService->settingPagination($arrayUser,$request->query->getInt('page',1),6);
        if($pagination === 'redirect'){
            return $this->redirectToRoute('profile_list_association');
        }
        return $this->render('profile/card_user.html.twig',[
            'users' => $pagination,
            'theme' => $users->getName()
        ]);
    }

    private function countBestTheme($entityAdh){
        $count = 0;
        $getTheme = [];
        $newTheme = '';
        foreach($entityAdh as $assoc){
            $getTheme[] = $assoc->getTheme()->getName();
        }
        $countTheme = array_count_values($getTheme);
        foreach($countTheme as $key => $theme){
            if($count < $theme){
                $count = $theme;
                $newTheme = $key;
            }
        }
        return ["theme" => $newTheme, "number" => $count];
    }

    private function updatePwd($pwdOld,$data){
        if(!is_null($pwdOld)){
            if (!$this->password->isPasswordValid($this->getUser(), $pwdOld)) {
                $this->addFlash('danger','Le mot de passe ne correspond pas à l\'ancien');
                return false;
            }elseif(!is_null($data->get('new_password')->getData())){
                $data->getData()->setPassword($this->password->encodePassword($data->getData(),$data->get('new_password')->getData()));
            }else{
                $this->addFlash('danger','Le nouveau mot de passe n\'est pas rempli');
                return false;
            }
        }else if(is_null($pwdOld) && !is_null($data->get('new_password')->getData())){
            $this->addFlash('danger','L\'ancien mot de passe n\'est pas rempli');
            return false;
        }
        return true;
    }

    private function form(array $arrayForm, $request){
        foreach($arrayForm as $key => $form){
            if(!empty($form)){
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()){
                    $data = $form;
                    if($key === 'user'){
                        $pwdOld = $form->get('old_password')->getData();
                        if($this->updatePwd($pwdOld,$data) === false){
                            return false;
                        };
                    }
                    $this->em->flush();
                    $this->addFlash('success','Modification réussie');
                }
            }
        }
    }

    /**
     * @Route("/modifier-profil", name="update")
     */
    public function edit(Request $request): Response {
        $currentUser = $this->getUser();
        $conditionUser = $this->isGranted('ROLE_ADH_CONFIRME');
        $typeClass = $conditionUser ? Adherent::class : Association::class;

        $typeUser = $this->em->getRepository($typeClass)->find($conditionUser ? $currentUser->getAdherent() : $currentUser->getAssociation());
        $form = $this->createForm($conditionUser ? UpdateAdherentType::class : UpdateAssocType::class, $typeUser);
        $formUser = $this->createForm(UpdateUserType::class,$currentUser);
        if(!$conditionUser){
            $formAddress = $this->createForm(AddressType::class,$currentUser->getAssociation()->getAddress());
            $formAdd = ["formAddress" => $formAddress->createView()];
        }

        $arrayForm = ["form" => $form, "user" => $formUser, "address" => !empty($formAddress) ? $formAddress : ''];
        $this->form($arrayForm,$request);
        if(($form->isSubmitted() && $form->isValid()) ||
            ($formUser->isSubmitted() && $formUser->isValid()) ||
            (!empty($formAddress) && $formAddress->isSubmitted() && $formAddress->isValid())){
            return $this->redirectToRoute("profile_update");
        }

        return $this->render('profile/update_profile.html.twig',[
            "form" => $form->createView(),
            "formUser" => $formUser->createView(),
            "address" => !$conditionUser ? $formAdd : '',
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
            $class = $getType ? Adherent::class : Association::class;
            $typeUser = $this->em->getRepository($class)->find($getType ? $getType : $currentUser->getAssociation());

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
     * @Route("/follow-suggestion", name="follow-suggestion")
     */
    public function followSuggestion(Request $request): Response
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

    /**
     * @Route("/follow", name="follow")
     */
    public function follow(Request $request): Response
    {
        header('Content-Type: application/json');
        try {
            $json_str = file_get_contents('php://input');
            $json_obj = json_decode($json_str);
            $association = $this->em->getRepository(Association::class)->find($json_obj);
            $adherent = $this->getUser()->getAdherent();
            if (!in_array($this->getUser()->getAdherent(), $association->getAdherents()->toArray())) {
                $association->addAdherent($adherent);
                $this->em->flush();
                return new JsonResponse(['title' => 'ET BOOM!💥', 'message' => 'Vous suivez desormais ' . $association->getName(), 'value' => 'add']);
            } else {
                $association->removeAdherent($adherent);
                $this->em->flush();
                return new JsonResponse(['title' => 'ET BOOM!💥', 'message' => 'Vous ne suivez plus ' . $association->getName(), 'value' => 'del']);
            }
        } catch (Error $e) {
            http_response_code(500);
            return new JsonResponse(['error' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/mes-follower", name="followers")
     */
    public function myfollowers(): Response
    {
        if (in_array('ROLE_ADH_CONFIRME',$this->getUser()->getRoles())){
            $followers = $this->getUser()->getAdherent()->getAssociations();
        }else{
            $followers = $this->getUser()->getAssociation()->getAdherents();
        }
        return $this->render('profile/follower.html.twig', [
            'followers'=> $followers
        ]);
    }
}
