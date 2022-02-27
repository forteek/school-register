<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Assignment;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CreateAssignmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Opis',
                'constraints' => [
                    new NotBlank(message: 'Podaj opis'),
                    new Length(
                        min: 2,
                        max: 65535,
                        minMessage: 'Opis powinien mieć minimum {{ limit }} znaki',
                        maxMessage: 'Opis powinien mieć maksimum {{ limit }} znaków',
                    ),
                ],
            ])
            ->add('dueDate', DateTimeType::class, [
                'label' => 'Termin oddania',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(message: 'Podaj termin oddania'),
                ],
            ])
            ->add('group', EntityType::class, [
                'label' => 'Klasa',
                'class' => Group::class,
                'choice_label' => 'symbol',
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('g')
                        ->orderBy('g.id', 'ASC')
                    ;
                },
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Utwórz',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Assignment::class,
        ]);
    }
}
