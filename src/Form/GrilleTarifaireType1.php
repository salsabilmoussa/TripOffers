<?php

namespace App\Form;

use App\Entity\Hotel;
use App\Entity\GrilleTarifaire;
use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GrilleTarifaireType1 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('prix')
            ->add('offreType', ChoiceType::class, [
                'choices' => [
                    'Voyage' => 'voyage',
                    'Omra' => 'omra',
                    'Excursion' => 'excursion',
                    'Croisière' => 'croisière',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => 'title', 
                'placeholder' => '',
                'required' => true,
            ])
            ->add('hotel', EntityType::class, [
                'class' => Hotel::class,
                'choice_label' => 'nom', 
                'placeholder' => '',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GrilleTarifaire::class,
        ]);
    }
}
