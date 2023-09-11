<?php
namespace App\Form\omra;

use App\Entity\Agence;
use App\Entity\Omra;
use App\Entity\Destination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class InfoGeneralType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title');
            if (!$this->isAgent()) {
                $builder->add('agence', EntityType::class, [
                    'class' => Agence::class,
                    'choice_label' => 'name', 
                    'placeholder' => '',
                    'required' => true,
                ]);
            };
            $builder 
            ->add('photo', FileType::class, [
                'label' => 'Photo (PDF file)',
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
            ])
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
           
        ;
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
