<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'événement',
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Conférence' => 'conference',
                    'Workshop' => 'workshop',
                    'Formation' => 'formation',
                    'Compétition' => 'competition',
                    'Autre' => 'autre',
                ],
            ])
            ->add('nbrPersonne', IntegerType::class, [
                'label' => 'Nombre de personnes',
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'TND',
            ])
            ->add('accesNonMembre', CheckboxType::class, [
                'label' => 'Accès aux non-membres',
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de l\'événement',
                'widget' => 'single_text',
            ])
            ->add('location', TextType::class, [
                'label' => 'Lieu',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}