<?php

namespace App\Controller\Profile;

use App\Entity\Association;
use App\Entity\Tag;
use App\Form\UpdatePwdType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TagsController extends AbstractController
{
    /**
     * @Route("/tags", name="tags")
     */
    public function index(): Response
    {
        $form = $this->createForm(UpdatePwdType::class);

        return $this->render('profile/suggestions.hmtl.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/getTags", name="getTags")
     */
    public function getTags(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository(Tag::class)->findAllTag();


        return new JsonResponse($tags);


    }

    /**
     * @Route("/manageTag", name="manageTag")
     */
    public function addTag(Request $request): JsonResponse
    {
        $tag = $request->get('tag');
        $toDelete = $request->get('manage') ? $request->get('manage') : false;
        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository(Tag::class)->findOneBy(['tag' => $tag]);


        if (!in_array($tag, $this->getUser()->getTags()->toArray())) {
            $this->getUser()->addTag($tag);
            $em->flush();
            return new JsonResponse(true);
        } elseif (in_array($tag, $this->getUser()->getTags()->toArray()) && $toDelete) {
            $this->getUser()->removeTag($tag);
            $em->flush();
            return new JsonResponse(true);
        }
        return new JsonResponse(false);
    }


    /**
     * @Route("/bestMatch", name="bestMatch")
     * @return JsonResponse
     *
     *
     * Pour avoir le meilleur match entre adhérant et association nous comparons les tags de l'adherant à ceux de l'association
     * la variable $tt représente le score ou 0 est un match parfait entre les tags associations et les tags user
     * Plus ce score est élevé moins il y a de corrélation entre les deux.
     *
     *
     * @version 1.0
     * @author  Yassine Bousaidi
     */
    public function bestMatch(): JsonResponse
    {
        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer([$normalizer], [$encoder]);

        $userTags = $this->getUser()->getTags();

        $userTagsCount = count($userTags->getValues());
        $em = $this->getDoctrine()->getManager();
        $associations = $em->getRepository(Association::class)->findAll();
        $bestResult = [];
        $tt = 0;
        // la variable $tt fais reference au score que l'on obtiens a chaque passage, on verifie si la soustraction des objet
        // fonctionne dans ce cas  on a un meilleur score et si tt incrémente le score est moins elevé donc on push apres
        for ($tt; $tt < $userTagsCount; $tt++) {
            foreach ($associations as $association) {
                if (count($bestResult) == 8) {
                    break;
                }
                $diff = array_udiff($userTags->getValues(), $association->getUserAccount()->getTags()->getValues(),
                    function ($obj_a, $obj_b) {
                        return $obj_a->getId() - $obj_b->getId();
                    }
                );
                $res = count($diff);
                if ($res == $tt) {
                    array_push($bestResult, $serializer->serialize($association,
                        'json',
                        [AbstractNormalizer::IGNORED_ATTRIBUTES => ['tags', 'adherents', 'association','event']]));
                }

            }

        }
        return new JsonResponse($bestResult);

    }


}
