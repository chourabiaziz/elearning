<?php 
namespace App\Controller;

use App\Entity\CustomService;
use App\Form\CustomServiceType;
use App\Repository\CustomServiceRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomServiceController extends AbstractController
{
    #[Route('/custom-service/create', name: 'create_custom_service')]
    public function create(Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les catégories pour le champ de sélection
        $categories = $categorieRepository->findAll();

        // Créer un nouveau CustomService
        $customService = new CustomService();

        $form = $this->createForm(CustomServiceType::class, $customService, [
            'categories' => $categories,
        ]);
        
        $form->handleRequest($request);

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
                    $customService->setImage($newFilename);
                } catch (\Exception $e) {
                    // En cas d'erreur, ajouter un message d'erreur
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            // Enregistrer dans la base de données
            $entityManager->persist($customService);
            $entityManager->flush();

            $this->addFlash('success', 'Service personnalisé créé avec succès.');

            return $this->redirectToRoute('custom_service_list');
        }

        return $this->render('custom_service/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/custom-service/{id}/edit', name: 'edit_custom_service')]
    public function edit(int $id, Request $request, CustomServiceRepository $customServiceRepository, CategorieRepository $categorieRepository, EntityManagerInterface $entityManager): Response
    {
        $customService = $customServiceRepository->find($id);

        if (!$customService) {
            throw $this->createNotFoundException('Le service personnalisé n\'existe pas.');
        }

        // Récupérer les catégories pour le champ de sélection
        $categories = $categorieRepository->findAll();

        $form = $this->createForm(CustomServiceType::class, $customService, [
            'categories' => $categories,
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image (comme dans la création)
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
                    $customService->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            // Enregistrer dans la base de données
            $entityManager->flush();

            $this->addFlash('success', 'Service personnalisé mis à jour avec succès.');

            return $this->redirectToRoute('custom_service_list');
        }

        return $this->render('custom_service/edit.html.twig', [
            'form' => $form->createView(),
            'customService' => $customService,
        ]);
    }

    #[Route('/custom-service/{id}/delete', name: 'delete_custom_service')]
    public function delete(int $id, CustomServiceRepository $customServiceRepository, EntityManagerInterface $entityManager): Response
    {
        $customService = $customServiceRepository->find($id);

        if (!$customService) {
            throw $this->createNotFoundException('Le service personnalisé n\'existe pas.');
        }

        $entityManager->remove($customService);
        $entityManager->flush();

        $this->addFlash('success', 'Service personnalisé supprimé avec succès.');

        return $this->redirectToRoute('custom_service_list');
    }

    #[Route('/custom-service', name: 'custom_service_list')]
    public function list(CustomServiceRepository $customServiceRepository): Response
    {
        $customServices = $customServiceRepository->findAll();

        return $this->render('custom_service/list.html.twig', [
            'customServices' => $customServices,
        ]);
    }
}
