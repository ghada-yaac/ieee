<?php

namespace App\Controller;
use App\Entity\Event;

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

    #[Route('/home', name: 'app_home')]
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
    public function home(): Response
    {
        return $this->render('home/acceuil.html.twig');
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
}
