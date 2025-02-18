<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Post;
use App\Form\CommentaireType;
use App\Form\PostType;
use App\Form\PostType2;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {





        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);



    }
    #[Route('/admin',name: 'app_post_index_admin')]
    public function inde2x(PostRepository $postRepository): Response
    {
        return $this->render('post/admin/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
    
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '.' . $imageFile->guessExtension();
    
                    try {
                        $imageFile->move(
                            $this->getParameter('upload_directory_posts'),
                            $newFilename
                        );
                        $post->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                        // Log the error for debugging purposes
                           }
                }else{
                    $this->addFlash('error', 'image est obligatoir.');
                    return $this->redirectToRoute('app_quiz_new_admin');
                }
             $post->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/admin/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    

    #[Route('/{id}', name: 'app_post_show' )]
    public function show(Post $post , EntityManagerInterface $entityManager ,Request $request): Response
    {
        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
      //  $request->request->get("contenu");
        if ($form->isSubmitted() && $form->isValid()) {
    
             $comment->setPost($post);
            $comment->setUsername("User");
            $comment->setCreatedAt(new \DateTimeImmutable());
    
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'commentaire ajouté avec succès!');

            return $this->redirectToRoute('app_post_show', ["id"=>$post->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->render('post/show.html.twig', [
            'post' => $post,            'form' => $form->createView(),

        ]);
    } 
    
    
    
    #[Route('/{id}/admin', name: 'app_post_show_admin')]
    public function showa(Post $post): Response
    {
        return $this->render('post/admin/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType2::class, $post);
        $form->handleRequest($request);
        $originalImage = $post->getImage(); // Store old filename
        if ($originalImage = $post->getImage()) {
            
        }else {
            $originalImage = "default.png";
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory_posts'), // Define this in services.yaml
                        $newFilename
                    );
                    // Delete the old image file if it exists
                    if ($originalImage) {
                        unlink($this->getParameter('upload_directory_posts').'/'.$originalImage);
                    }
                    $post->setImage($newFilename);
                } catch (FileException $e) {
                    // Handle exception
                }
            } else {
                // Keep the old image
                $post->setImage($originalImage);
            }
            $entityManager->persist($post); // requete sql pour enregistreent
            $entityManager->flush(); //execurtion du requete

            return $this->redirectToRoute('app_post_index_admin');
        }

        return $this->render('post/admin/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/delete/admin/{id}', name: 'app_post_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
             $entityManager->remove($post);
            $entityManager->flush();

        return $this->redirectToRoute('app_post_index_admin', [], Response::HTTP_SEE_OTHER);
    }
}
