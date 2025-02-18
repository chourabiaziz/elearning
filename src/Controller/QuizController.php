<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\FormationRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quiz')]
final class QuizController extends AbstractController
{
    #[Route(name: 'app_quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

  
    #[Route('/{id}', name: 'app_quiz_show', methods: ['GET'])]
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_show_admin',['id'=>$quiz->getFormation()->getId()]);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_show_admin',['id'=>$quiz->getFormation()->getId()]);
    }


    #[Route('/new/{id}/admin', name: 'app_quiz_new_admin', methods: ['GET', 'POST'])]
    public function aaaabb(Request $request, int $id, FormationRepository $formationRepository, EntityManagerInterface $entityManager): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            if ($quiz->getCorrect() != $quiz->getIncorrect1() && $quiz->getCorrect() != $quiz->getIncorrect2()) {
    
                // Handle image upload
                $imageFile = $form->get('image')->getData();
    
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '.' . $imageFile->guessExtension();
    
                    try {
                        $imageFile->move(
                            $this->getParameter('upload_directory'),
                            $newFilename
                        );
                        $quiz->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                        // Log the error for debugging purposes
                           }
                }else{
                    $this->addFlash('error', 'image est obligatoir.');
                    return $this->redirectToRoute('app_quiz_new_admin', ['id' => $id]);
                }
    
                // Set related 'formationx'
                $formationxFromUrl = $formationRepository->find($id);
                if ($formationxFromUrl) {
                    $quiz->setformation($formationxFromUrl);
                }
    
                $entityManager->persist($quiz);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_formation_show_admin',['id'=>$id]);
            } else {
                $this->addFlash('error', 'Les réponses doivent être différentes.');
                return $this->redirectToRoute('app_quiz_new_admin', ['id' => $id]);
            }
        }
    
        return $this->render('quiz/admin/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }
}
