<?php

namespace App\Controller\Search;

use App\Entity\Adherent;
use App\Entity\Association;
use App\Repository\AdherentRepository;
use App\Repository\AssociationRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/lists", name="lists")
     */
    public function autoComplete(){
        $data = [];
        $adherents = $this->em->getRepository(Adherent::class)->findAll();
        $associations = $this->em->getRepository(Association::class)->findAll();
        foreach([$adherents,$associations] as $array){
            foreach($array as $object){
                if($object instanceof Adherent){
                    array_push($data, $object->getFirstname() . ' ' . $object->getLastname() . ' | ' . 'Adhérent');
                }elseif($object instanceof Association){
                    array_push($data, $object->getName() . ' | ' . 'Association');
                }
            }
        }
        sort($data);
        return new JsonResponse($data);
    }

    /**
     * @Route("/reseau", name="search")
     */
    public function search(PaginationService $paginationService, Request $request, AdherentRepository $adherent, AssociationRepository $association){
        $search = $request->query->get('search');
        if(!is_null($search) && !empty($search)){
            $search = preg_match('/[|]/',$search) ? trim(explode('|',$search)[0]) : $search;
            $dataAdherent = $adherent->searchAdherent($search);
            $dataAssociation = $association->searchAssociation($search);
        }elseif(is_null($search) || empty($search)) {
            $dataAdherent = $adherent->findAll();
            $dataAssociation = $association->findAll();
        }
        $data = array_merge($dataAdherent,$dataAssociation);

        $pagination = $paginationService->settingPagination($data,$request->query->getInt('page',1),6);
        if($pagination === 'redirect'){
            return $this->redirectToRoute('default_connect');
        }

        return $this->render('profile/card_user.html.twig',[
            "users" => $pagination,
            "search" => $search
        ]);
    }
}
