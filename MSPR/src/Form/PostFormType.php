<?php

namespace App\Form;

use App\Entity\Plant;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PlantRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('image',FileType::class)
            ->add('plantId',EntityType::class, [
                'placeholder' => 'Choose an option',
                'class' => Plant::class,
                'query_builder' => function (PlantRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
                'choice_label' => 'label',
            ])
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
