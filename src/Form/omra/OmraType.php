<?php

namespace App\Form\omra;

use App\Entity\Hotel;
use App\Entity\Agence;
use App\Entity\Omra;
use App\Entity\Excursion;
use App\Entity\Destination;
use App\Entity\GrilleTarifaire;
use Doctrine\ORM\EntityRepository;
use App\Repository\HotelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;


class OmraType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('photo', FileType::class, [
                'label' => 'photo (PDF file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ]);
        if (!$this->isAgent()) {
            $builder->add('agence', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => 'name',
                'placeholder' => '',
                'required' => true,
            ]);
        };
        $builder
            ->add('inclus')
            ->add('nonInclus')
            ->add('images', FileType::class, [
                'label' => 'Images',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All([
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/jpg',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid image',
                        ])
                    ])
                ],

            ])
            ->add('grilletarifaire_title', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Grille tarifaire - Titre',

            ])
            ->add('grilletarifaire_date_debut', DateType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Grille tarifaire - Date de début',
            ])
            ->add('grilletarifaire_date_fin', DateType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Grille tarifaire - Date de fin',
            ])
            ->add('grilletarifaire_prix', MoneyType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Grille tarifaire - Prix',
                'currency' => 'TND',
            ])
            ->add('hotel', EntityType::class, [
                'class' => Hotel::class,
                'choice_label' => 'nom', 
                'placeholder' => '',
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Omra::class,
        ]);
    }

    private function isAgent(): bool
    {
        $user = $this->security->getUser();
        return $this->security->isGranted('ROLE_AGENT');
    }
}
