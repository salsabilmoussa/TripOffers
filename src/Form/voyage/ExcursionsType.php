<?php

namespace App\Form\voyage;

use App\Entity\Voyage;
use App\Entity\Excursion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExcursionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('excursion', EntityType::class, [
            'class' => Excursion::class,
            'choice_label' => 'title', 
            'multiple' => true,
            'expanded' => true, 
            'attr' => ['size' => 5],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voyage::class,
        ]);
    }
}
