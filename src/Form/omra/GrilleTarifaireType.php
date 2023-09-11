<?php

namespace App\Form\omra;

use App\Entity\Hotel;
use App\Entity\GrilleTarifaire;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class GrilleTarifaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('prix')
            ->add('hotel', EntityType::class, [
                'class' => Hotel::class,
                'choice_label' => 'nom',
                'placeholder' => '',
                'required' => true,
                'query_builder' => function (EntityRepository $er) use ($options) {
                   
                    // Requête personnalisée pour charger les hôtels avec le même lieu que la destination
                    return $er->createQueryBuilder('h')
                        ->where('h.lieu = :lieu')
                        ->setParameter('lieu', 'Arabie saoudite');
                },
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GrilleTarifaire::class,
        ]);
    }
}
