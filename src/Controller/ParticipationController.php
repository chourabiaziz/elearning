<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participation;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ParticipationController extends AbstractController
{
    #[Route('/participation', name: 'app_participation')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->render('participation/show.html.twig', [
            'event_liste' => $events,
        ]);
    }

    #[Route('/participation/join/{id}', name: 'app_participation_join')]
    #[IsGranted('roles')]
    public function join(Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($event->getSeuil() <= 0) {
            $this->addFlash('error', 'Cet événement est complet !');
            return $this->redirectToRoute('app_participation');
        }

        $participation = new Participation();
        $participation->setIdUser($this->getUser());
        $participation->setIdEvent($event);
        $participation->setDateParticipation(new \DateTime());
        $participation->setStatus('Confirmed');

        $event->setSeuil($event->getSeuil() - 1);

        $entityManager->persist($participation);
        $entityManager->persist($event);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez rejoint l\'événement avec succès !');

        return $this->redirectToRoute('app_participation');
    }
}
