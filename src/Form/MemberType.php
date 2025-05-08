<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('memberID')
            ->add('roles')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('numTel')
            ->add('membershipStatus')
            ->add('membershipStartDate', null, [
                'widget' => 'single_text',
            ])
            ->add('membershipEndDate', null, [
                'widget' => 'single_text',
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
