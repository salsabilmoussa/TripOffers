<?php

namespace App\Form\voyage;

use App\Entity\Hotel;
use App\Entity\GrilleTarifaire;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrilleTarifaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $destination = $options['data']->getOffre()->getDestination()->getName();
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
                'query_builder' => function (EntityRepository $er) use ($destination) {

                    // Requête personnalisée pour charger les hôtels avec le même lieu que la destination
                    return $er->createQueryBuilder('h')
                        ->where('h.lieu = :lieu')
                        ->setParameter('lieu', $destination);
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
