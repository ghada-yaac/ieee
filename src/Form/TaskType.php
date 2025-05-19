<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Member;
use App\Entity\Task;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la tâche',
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Organisation' => 'organisation',
                    'Logistique' => 'logistique',
                    'Communication' => 'communication',
                    'Technique' => 'technique',
                    'Autre' => 'autre',
                ],
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'À faire' => 'todo',
                    'En cours' => 'in_progress',
                    'Terminée' => 'completed',
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date limite',
                'widget' => 'single_text',
            ])
            ->add('point', IntegerType::class, [
                'label' => 'Points',
            ])
            ->add('relation', EntityType::class, [
                'class' => Member::class,
                'label' => 'Membre responsable',
                'choice_label' => function (Member $member) {
                    return $member->getNom() . ' ' . $member->getPrenom();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->where('m.membershipStatus = :status')
                        ->setParameter('status', 'active')
                        ->orderBy('m.nom', 'ASC');
                },
                'required' => false,
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'label' => 'Événement',
                'choice_label' => 'nom',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.date', 'DESC');
                },
                'disabled' => $options['event_disabled'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'event_disabled' => false,
        ]);
    }
}