<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CreateStudentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Imię',
                'constraints' => [
                    new NotBlank(message: 'Podaj imię'),
                    new Length(
                        min: 2,
                        max: 255,
                        minMessage: 'Imię powinno mieć minimum {{ limit }} znaki',
                        maxMessage: 'Imię powinno mieć maksimum {{ limit }} znaków',
                    ),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nazwisko',
                'constraints' => [
                    new NotBlank(message: 'Podaj nazwisko'),
                    new Length(
                        min: 2,
                        max: 255,
                        minMessage: 'Nazwisko powinno mieć minimum {{ limit }} znaki',
                        maxMessage: 'Nazwisko powinno mieć maksimum {{ limit }} znaków',
                    ),
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Zdjęcie',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Zdjęcie powinno mieć format PNG lub JPG',
                    ]),
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
            'data_class' => Student::class,
        ]);
    }
}
