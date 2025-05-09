<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Member();
        $lastMember = $entityManager->getRepository(Member::class)->findOneBy([], ['memberID' => 'DESC']);
        $lastId = $lastMember ? (int) $lastMember->getMemberID() : 1000;

        $newMemberID = $lastId + 1;
        $user->setPoints(0);
        $user->setMemberID($newMemberID);
        $user->setMembershipStartDate(new \DateTime());
        $user->setMembershipEndDate((new \DateTime())->modify('+1 year'));
        $user->setMembershipStatus('pending');
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
            // do anything else you need here, like send an email


        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
