<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Post;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CommentaireController extends AbstractController
{
    #[Route('/post/{id}/comment/edit', name: 'app_post_comment', methods: ['POST', 'GET'])]
    public function addComment(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            $this->addFlash('success', 'commentaire mise a jour avec succès!');

            return $this->redirectToRoute('app_post_show', ["id"=>$commentaire->getPost()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/post/{id}/comment/delete', name: 'app_post_comment_delete')]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
 

             $entityManager->remove($commentaire);
            $entityManager->flush();
            $this->addFlash('success', 'commentaire supprimé avec succès!');

            return $this->redirectToRoute('app_post_show', ["id"=>$commentaire->getPost()->getId()], Response::HTTP_SEE_OTHER);
     
    }
    
}
