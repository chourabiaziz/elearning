<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/formation')]
final class FormationController extends AbstractController
{
    #[Route(name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }


 
    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

 
    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new/admin', name: 'app_formation_new_admin')]
     public function newadmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $videoFile = $form->get('video')->getData(); // Assuming 'video' is the field name
    
            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '.' . $videoFile->guessExtension();
    
                try {
                    $videoFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                    $formation->setVideo($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de la vidéo.');
                    return $this->redirectToRoute('app_formation_new_admin'); // Redirect to avoid further execution
                }
            } else {
                $this->addFlash('error', 'La vidéo est obligatoire.');
                return $this->redirectToRoute('app_formation_new_admin'); // Stop execution if file is missing
            }
    
            $entityManager->persist($formation);
            $entityManager->flush();
    
            $this->addFlash('success', 'Formation ajoutée avec succès!');
            return $this->redirectToRoute('app_formation_index'); // Redirect to the index page
        }
    
        return $this->render('formation/admin/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    









    #[Route('/admin/listes',name: 'app_formation_index_admin')]
    public function admin(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/admin/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/etudiant/submit/{id}', name: 'formation_submit')]
    public function submit(FormationRepository $quizRepository, int $id, Request $request): Response
    {
        // Fetch the game by ID
        $formation = $quizRepository->find($id);
        if (!$formation) {
            throw $this->createNotFoundException('forma not found');
        }
    
        // Debugging: Check if the form is being submitted
        dump($request->isMethod('POST')); // Should return true if the form is submitted
    
        // Get the answers submitted by the user (array format)
        $userAnswers = $request->request->all('answers'); // Use `all()` to retrieve the entire array
    
        // Debugging: Check submitted answers
        dump($userAnswers);
    
        // If answers are empty, return a response for debugging
        if (empty($userAnswers)) {
            return new Response('No answers submitted. Check the form submission.', 400);
        }
    
        // Initialize variables to store results
        $correctAnswers = [];
        $totalCorrect = 0;
    
        // Process the answers
        foreach ($formation->getQuiz() as $quiz) {
            // Get the user answer for the current quiz (if it exists)
            $userAnswer = $userAnswers[$quiz->getId()] ?? null;
    
            // Trim and normalize both values for comparison
            $userAnswer = trim($userAnswer);
            $correctAnswer = trim($quiz->getCorrect());
    
            // Check if the user's answer matches the correct one
            $isCorrect = ($userAnswer === $correctAnswer);
    
            // Store the result (whether the answer is correct or not)
            $correctAnswers[$quiz->getId()] = [
                'quiz' => $quiz,
                'is_correct' => $isCorrect,
            ];
    
            // Increment the score if the answer is correct
            if ($isCorrect) {
                $totalCorrect++;
            }
        }
    
        // Calculate the total number of quizs
        $totalquizs = count($formation->getQuiz());
    
        // Pass results to the template (including the correct answers and score)
        return $this->render('quiz/results.html.twig', [
            'formation' => $formation,
            'correct_answers' => $correctAnswers,
            'score' => $totalCorrect,
            'total' => $totalquizs,  // Total number of flashcards
        ]);
    }
    #[Route('/amin/{id}', name: 'app_formation_show_etud', methods: ['GET'])]
    public function show_amin(Formation $formation): Response
    {
        return $this->render('formation/amin/show.html.twig', [
            'formation' => $formation,
        ]);
    }
    #[Route('/admin/{id}', name: 'app_formation_show_admin', methods: ['GET'])]
    public function admin_show(Formation $formation): Response
    {
        return $this->render('formation/admin/show.html.twig', [
            'formation' => $formation,
        ]);
    }
    #[Route('/{id}/edit/admin', name: 'app_formation_edit_admin', methods: ['GET', 'POST'])]
    public function aaaaa(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/admin/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }


}
