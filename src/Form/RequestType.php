<?php

namespace App\Form;

use App\Entity\GuestRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'attr' => ['placeholder' => 'Your Name']
        ])
        ->add('email', EmailType::class, [
            'attr' => ['placeholder' => 'example@gmail.com']
        ])
            ->add('phone', TextType::class, [
                'attr' => ['placeholder' => '+123 (456) 789-0123']])
            ->add('message')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GuestRequest::class,
        ]);
    }
}
