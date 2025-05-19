<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use App\Repository\EventRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(MemberRepository $memberRepository, EventRepository $eventRepository, TaskRepository $taskRepository): Response
    {
        // Récupérer les statistiques pour le dashboard
        $totalMembers = $memberRepository->count([]);
        $activeMembers = $memberRepository->count(['membershipStatus' => 'active']);
        $pendingMembers = $memberRepository->count(['membershipStatus' => 'pending']);

        $totalEvents = $eventRepository->count([]);
        $upcomingEvents = $eventRepository->countUpcomingEvents();

        $totalTasks = $taskRepository->count([]);
        $completedTasks = $taskRepository->count(['statut' => 'completed']);

        // Données pour les graphiques
        $membersByRole = $memberRepository->countByRole();
        $eventsByCategory = $eventRepository->countByCategory();

        return $this->render('admin/dashboard.html.twig', [
            'totalMembers' => $totalMembers,
            'activeMembers' => $activeMembers,
            'pendingMembers' => $pendingMembers,
            'totalEvents' => $totalEvents,
            'upcomingEvents' => $upcomingEvents,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'membersByRole' => json_encode($membersByRole),
            'eventsByCategory' => json_encode($eventsByCategory),
        ]);
    }
}