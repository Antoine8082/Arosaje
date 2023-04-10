<?php

namespace App\Form;

use App\Entity\Plant;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PlantRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('image')
            ->add('user_post')
            ->add('plantId',EntityType::class, [
                'class' => Plant::class,
                'query_builder' => function (PlantRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
                'choice_label' => 'email',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
