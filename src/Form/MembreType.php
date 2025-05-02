<?php

namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('memberID')
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('mdp')
            ->add('numTel')
            ->add('membershipStatus')
            ->add('membershipStartDate', null, [
                'widget' => 'single_text',
            ])
            ->add('membershipEndDate', null, [
                'widget' => 'single_text',
            ])
            ->add('role')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
