<?php

namespace App\Controller\Publication;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{
    /**
     * @Route("/publication", name="publication")
     */
    public function index(): Response
    {
        return $this->render('publication/index.html.twig', [
            'controller_name' => 'PublicationController',
        ]);
    }

    /**
     * @Route ("/publication/new", name="publication_create")
     */
    public function createPublication(): Response
    {
        // creates a task object and initializes some data for this example
        $publication = new Publication();
        $publication->setPublication('Ajouter une nouvelle publication');

        $form = $this->createForm(publicationType::class, $publication);

    }

    /**
     * @Route("/publication/{id}", name="publication")
     */
    /*public function show(Publication $publication): Response
    {
        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
        ]);
    }*/

    /*public function show(Request $request, Publication $publication): Response
    {
    +        $comment = new Comment();
    +        $form = $this->createForm(CommentFormType::class, $comment;
    +
    $offset = max(0, $request->query->getInt('offset', 0));
    $paginator = $commentRepository->getCommentPaginator($publication, $offset);

    @@ -43,6 +48,7 @@ class PublicationController extends AbstractController
         'comments' => $paginator,
         'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
         'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
+            'comment_form' => $form->createView(),
     ]));
    }*/
}
