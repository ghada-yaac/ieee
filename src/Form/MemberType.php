<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('memberID', TextType::class, [
                'label' => 'ID Membre',
                'disabled' => true,
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('numTel', IntegerType::class, [
                'label' => 'Numéro de téléphone',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Bureau' => 'ROLE_BUREAU',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('membershipStatus', ChoiceType::class, [
                'label' => 'Statut d\'adhésion',
                'choices' => [
                    'Actif' => 'active',
                    'En attente' => 'pending',
                    'Inactif' => 'inactive',
                ],
            ])
            ->add('membershipStartDate', DateType::class, [
                'label' => 'Date de début d\'adhésion',
                'widget' => 'single_text',
            ])
            ->add('membershipEndDate', DateType::class, [
                'label' => 'Date de fin d\'adhésion',
                'widget' => 'single_text',
            ])
            ->add('points', IntegerType::class, [
                'label' => 'Points',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}