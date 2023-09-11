<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'off', // Désactiver l'autocomplétion
                ],])
            ->add('prenom', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'off', // Désactiver l'autocomplétion
                ],])
            ->add('numeroDeTelephone', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'off', // Désactiver l'autocomplétion
                ],])
            ->add('adresse', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'off', // Désactiver l'autocomplétion
                ],])
            ->add('message', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
