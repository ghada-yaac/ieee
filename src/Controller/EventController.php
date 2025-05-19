<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin/event')]
#[IsGranted('ROLE_ADMIN')]
class EventController extends AbstractController
{
    #[Route('/', name: 'event_index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        // Récupérer les filtres
        $category = $request->query->get('category');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        // Appliquer les filtres
        $events = $eventRepository->findByFilters($category, $startDate, $endDate);

        // Récupérer toutes les catégories pour le filtre
        $categories = $eventRepository->findAllCategories();

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès.');
            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'event_show', methods: ['GET'])]
    public function show(Event $event, ParticipantRepository $participantRepository, TaskRepository $taskRepository): Response
    {
        // Récupérer les participants à cet événement
        $participants = $participantRepository->findBy(['event_id' => $event->getId()]);

        // Récupérer les tâches liées à cet événement
        $tasks = $taskRepository->findBy(['event' => $event]);

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'participants' => $participants,
            'tasks' => $tasks,
        ]);
    }

    #[Route('/{id}/edit', name: 'event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');
            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été supprimé avec succès.');
        }

        return $this->redirectToRoute('event_index');
    }
}