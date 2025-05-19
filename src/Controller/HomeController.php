<?php

namespace App\Controller;
use App\Entity\Event;

use App\Entity\Task;
use App\Form\MemberEditType;
use App\Repository\EventRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class HomeController extends AbstractController
{
    #[Route('/home/pending', name: 'app_home_pending')]
    public function pending(): Response
    {
        return $this->render('home/pending.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/home/officers', name: 'app_home_officers')]
    public function officers(): Response
    {
        return $this->render('home/officers.html.twig');
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/home/register/{id}', name: 'app_event_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        return $this->render('home/register.html.twig', [
            'event' => $event,
        ]);
    }
    #[Route('/home/tasks/{id}', name: 'app_event_tasks')]
    public function tasks(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id,
        TaskRepository $taskRepository
    ): Response {
        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé.');
        }

        $tasks = $taskRepository->findByEventId($id);

        return $this->render('home/tasks.html.twig', [
            'event' => $event,
            'tasks' => $tasks,
        ]);
    }

    #[Route('/home/member', name: 'app_home_member')]
    public function home(TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();

        $taskCount = $taskRepository->count(['relation' => $user]);
        return $this->render('home/acceuil.html.twig', [
            'user' => $user,
            'taskCount' => $taskCount,
        ]);
    }
    #[Route('/home/contact', name: 'app_home_contact')]
    public function contact(): Response
    {

        return $this->render('home/contact.html.twig'
        );
    }
    #[Route('/home/event', name: 'app_home_event')]
    public function event(EventRepository $eventRepository): Response
    {

        return $this->render('home/evenement.html.twig',[
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/home/profil', name: 'app_profil_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(MemberEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('app_home_member');
        }

        return $this->render('home/profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/assign', name: 'app_task_assign', methods: ['POST'])]
    public function assign(Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($task->getRelation() !== null) {
            $this->addFlash('error', 'Cette tâche est déjà assignée.');
            return $this->redirectToRoute('app_task_index');
        }

        /** @var Member $user */
        $user = $this->getUser();
        $task->setRelation($user);
        $task->setStatut('in progress');
        $entityManager->flush();

        $this->addFlash('success', 'Tâche assignée avec succès.');

        return $this->redirectToRoute('app_home_event');
    }
    #[Route('/home/my-tasks', name: 'app_home_my_tasks')]
    public function myTasks(TaskRepository $taskRepository): Response
    {
        /** @var \App\Entity\Member $user */
        $user = $this->getUser();

        // Sélectionner les tâches assignées à l'utilisateur connecté
        $tasks = $taskRepository->findBy(['relation' => $user]);

        return $this->render('home/myTasks.html.twig', [
            'tasks' => $tasks,
        ]);
    }
    #[Route('/task/complete/{id}', name: 'app_task_complete', methods: ['POST'])]
    public function completeTask(Task $task, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        // Vérifie que la tâche appartient à l'utilisateur connecté
        if ($task->getRelation() !== $user) {
            $this->addFlash('error', 'Vous ne pouvez compléter que vos propres tâches.');
            return $this->redirectToRoute('app_home_member');
        }

        // Marquer comme terminée
        if ($task->getStatut() !== 'completed') {
            $task->setStatut('completed');

            // Récupérer les points de la tâche
            $pointsToAdd = $task->getPoint() ?? 0;

            // Ajouter les points à l'utilisateur
            $user->setPoints($user->getPoints() + $pointsToAdd);

            $entityManager->flush();

            $this->addFlash('success', 'Tâche marquée comme terminée. Vous avez gagné ' . $pointsToAdd . ' points !');
        } else {
            $this->addFlash('info', 'Cette tâche est déjà terminée.');
        }

        return $this->redirectToRoute('app_home_member');
    }


}
