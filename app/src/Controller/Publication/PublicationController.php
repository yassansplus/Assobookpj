<?php

namespace App\Controller\Publication;

use App\Entity\Association;
use App\Entity\Publication;
use App\Form\PublicationType;
use App\Form\UpdatePwdType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PublicationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/publication")
 */
class PublicationController extends AbstractController
{
    /**
     * @Route("/", name="publication_index", methods={"GET", "POST"})
     */
    public function index(PublicationRepository $publicationRepository, Request $request): Response
    {
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);
        $form = $this->createForm(UpdatePwdType::class);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $publication = $publicationRepository->find($request->get("publication"));
            $comment->setPublication($publication);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('publication_index');
        }

        return $this->render('publication/index.html.twig', [
            'publications' => $publicationRepository->findBy(["association" => $this->getUser()->getAssociation()]),
            'formComment' => $formComment->createView(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list/{id}", name="publication_by_id", methods={"GET"})
     */
    public function publicationById(Association $association): Response
    {
        $form = $this->createForm(UpdatePwdType::class);
        return $this->render('publication/index.html.twig', [
            'publications' => $association->getPublications(),
            'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/new", name="publication_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ASSOC_CONFIRME")
     */
    public function new(Request $request): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $publication->setAssociation($this->getUser()->getAssociation());
            $publication->setDatePublication();
            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('publication_index');
        }

        return $this->render('publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="publication_show", methods={"GET"})
     * @IsGranted("ROLE_ASSOC_CONFIRME")
     */
    public function show(Publication $publication): Response
    {
        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="publication_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Publication $publication): Response
    {
        if ($this->getUser()->getAssociation() != $publication->getAssociation()) {
            return $this->redirectToRoute('publication_index');
        }
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('publication_index');
        }

        return $this->render('publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="publication_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Publication $publication): Response
    {
        if ($this->getUser()->getAssociation() != $publication->getAssociation()) {
            return $this->redirectToRoute('publication_index');
        }
        if ($this->isCsrfTokenValid('delete' . $publication->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($publication);
            $entityManager->flush();
        }

        return $this->redirectToRoute('publication_index');
    }
}
