<?php

namespace App\Controller;

use App\Form\EventType;
use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/listevent', name: 'list_event')]
    public function showEvent(EventRepository $rep): Response
    {
        $list = $rep->findAll();
        return $this->render('event/showEvent.html.twig', [
            'event_liste' => $list,
        ]);
    }

    #[Route('/ajouter', name: 'ajouter_event', methods: ['GET', 'POST'])]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageE')->getData(); // Assurez-vous que le champ dans EventType s'appelle bien 'imageE'

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Déplacer l'image dans le dossier 'image'
                    $imageFile->move(
                        $this->getParameter('upload_directory'), // Défini dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier');
                    return $this->redirectToRoute('ajouter_event'); // Rediriger si erreur
                }

                // Enregistrer le nom du fichier dans l'événement
                $event->setImageE($newFilename);
            }

            // Persister l'événement dans la base de données
            $entityManager->persist($event);
            $entityManager->flush();

            // Flash success message
            $this->addFlash('success', 'Événement ajouté avec succès!');

            return $this->redirectToRoute('list_event');
        }

        return $this->render('event/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/delete/{id}", name: "delete")]
    public function delete($id, EventRepository $eventRepository, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepository->find($id);

        if ($event) {
            $entityManager->remove($event);
            $entityManager->flush();

            // Flash success message
            $this->addFlash('success', 'Événement supprimé avec succès!');
        } else {
            // Flash error message if the event is not found
            $this->addFlash('error', 'Événement non trouvé.');
        }

        return $this->redirectToRoute('list_event');
    }

    #[Route('/event/update/{id}', name: 'update_event')]
    public function update($id, Request $request, EventRepository $eventRepository, EntityManagerInterface $entityManager): Response
    {
        $event = $eventRepository->find($id);

        // Vérification si l'événement existe
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        // Créer le formulaire pour l'événement
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification si une nouvelle image a été téléchargée
            $imageFile = $form->get('imageE')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Déplacer l'image dans le dossier public/image
                    $imageFile->move(
                        $this->getParameter('upload_directory'), // Utilisation du paramètre upload_directory
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion d'erreur lors du téléchargement
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                    return $this->redirectToRoute('update_event', ['id' => $id]); // Rediriger vers la page de mise à jour
                }

                // Mettre à jour l'image de l'événement avec le nouveau nom de fichier
                $event->setImageE($newFilename);
            }

            // Persister la mise à jour de l'événement dans la base de données
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Événement mis à jour avec succès!');

            // Rediriger vers la liste des événements
            return $this->redirectToRoute('list_event');
        }

        return $this->render('event/update.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }
}





