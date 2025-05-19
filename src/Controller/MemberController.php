<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/member')]
#[IsGranted('ROLE_ADMIN')]
class MemberController extends AbstractController
{
    #[Route('/', name: 'member_index', methods: ['GET'])]
    public function index(Request $request, MemberRepository $memberRepository): Response
    {
        // Récupérer les filtres
        $role = $request->query->get('role');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');
        $status = $request->query->get('status');

        // Appliquer les filtres
        $members = $memberRepository->findByFilters($role, $startDate, $endDate, $status);

        // Récupérer tous les rôles pour le filtre
        $roles = $memberRepository->findAllRoles();

        return $this->render('member/index.html.twig', [
            'members' => $members,
            'roles' => $roles,
        ]);
    }

    #[Route('/{id}', name: 'member_show', methods: ['GET'])]
    public function show(Member $member): Response
    {
        return $this->render('member/show.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/{id}/edit', name: 'member_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Member $member, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le membre a été modifié avec succès.');
            return $this->redirectToRoute('member_index');
        }

        return $this->render('member/edit.html.twig', [
            'member' => $member,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/status', name: 'member_change_status', methods: ['POST'])]
    public function changeStatus(Request $request, Member $member, EntityManagerInterface $entityManager): Response
    {
        $status = $request->request->get('status');

        if (in_array($status, ['active', 'pending', 'inactive'])) {
            $member->setMembershipStatus($status);
            $entityManager->flush();

            $this->addFlash('success', 'Le statut du membre a été modifié avec succès.');
        } else {
            $this->addFlash('error', 'Statut invalide.');
        }

        return $this->redirectToRoute('member_index');
    }

    #[Route('/{id}', name: 'member_delete', methods: ['POST'])]
    public function delete(Request $request, Member $member, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$member->getId(), $request->request->get('_token'))) {
            $entityManager->remove($member);
            $entityManager->flush();

            $this->addFlash('success', 'Le membre a été supprimé avec succès.');
        }

        return $this->redirectToRoute('member_index');
    }
}