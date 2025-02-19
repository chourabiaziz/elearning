<?php
// src/Controller/CoursController.php
namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/cours')]
class CoursController extends AbstractController
{
    #[Route('/', name: 'cours_index')]
    public function index(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findAll();

        return $this->render('cours/index.html.twig', [
            'cours' => $cours,
        ]);
    }

   
    #[Route('/ajouter', name: 'ajouter_cours', methods: ['GET', 'POST'])]
    public function ajouterCours(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
    
                try {
                    // Déplacer l'image dans le dossier 'public/image'
                    $imageFile->move(
                        $this->getParameter('image_directory'),  // Utilisez 'public/image' comme répertoire
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion d'erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier image');
                }
    
                // Enregistrer le nom du fichier image dans le cours
                $cours->setImage($newFilename);
            }
    
            // Récupérer et gérer la vidéo
            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();
    
            if ($videoFile) {
                $newVideoFilename = uniqid() . '.' . $videoFile->guessExtension();
    
                try {
                    // Déplacer la vidéo dans le dossier 'public/video'
                    $videoFile->move(
                        $this->getParameter('video_directory'),  // Utilisez 'public/video' comme répertoire
                        $newVideoFilename
                    );
                } catch (FileException $e) {
                    // Gestion d'erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier vidéo');
                }
    
                // Enregistrer le nom du fichier vidéo dans le cours
                $cours->setVideo($newVideoFilename);
            }
    
            // Persister le cours dans la base de données
            $entityManager->persist($cours);
            $entityManager->flush();
    
            // Rediriger vers la liste des cours
            return $this->redirectToRoute('list_cours');
        }
    
        return $this->render('cours/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/liste', name: 'list_cours')]
public function listCours(CoursRepository $coursRepository): Response
{
    $cours = $coursRepository->findAll();

    return $this->render('cours/index.html.twig', [
        'cours' => $cours,
    ]);
}



#[Route('/cours/{id}', name: 'show_cours')]
public function showCours(int $id, CoursRepository $coursRepository): Response
{
    $cours = $coursRepository->find($id);
    if (!$cours) {
        throw $this->createNotFoundException('Le cours n\'existe pas.');
    }
    return $this->render('cours/show.html.twig', ['cours' => $cours]);
}



#[Route('/modifier/{id}', name: 'update_cours')]
public function updateCours(int $id, Request $request, EntityManagerInterface $entityManager, CoursRepository $coursRepository): Response
{
    // Trouver le cours à modifier
    $cours = $coursRepository->find($id);

    // Si le cours n'existe pas, rediriger ou afficher une erreur
    if (!$cours) {
        throw $this->createNotFoundException('Le cours n\'existe pas.');
    }

    $form = $this->createForm(CoursType::class, $cours);
    $form->handleRequest($request);

    // Si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le fichier d'image téléchargé
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            // Générer un nouveau nom unique pour le fichier
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            try {
                // Déplacer l'image dans le dossier public/image
                $imageFile->move(
                    $this->getParameter('image_directory'), // Répertoire où l'image sera stockée
                    $newFilename
                );

                // Mettre à jour le nom de l'image dans la base de données
                $cours->setImage($newFilename);
            } catch (FileException $e) {
                // En cas d'erreur lors de l'upload, ajouter un message d'erreur
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
            }
        }

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Rediriger vers la page de liste des cours
        return $this->redirectToRoute('cours_index');
    }

    return $this->render('cours/update.html.twig', [
        'form' => $form->createView(),
        'cours' => $cours,
    ]);
}

#[Route('/cours/{id}/supprimer', name: 'delete_cours')]
public function deleteCours(int $id, CoursRepository $coursRepository, EntityManagerInterface $entityManager): Response
{
    $cours = $coursRepository->find($id);
    if (!$cours) {
        throw $this->createNotFoundException('Le cours n\'existe pas.');
    }
    $entityManager->remove($cours);
    $entityManager->flush();

    return $this->redirectToRoute('cours_index');
}


}
