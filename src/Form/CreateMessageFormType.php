<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Message;
use App\Entity\Student;
use App\Entity\User;
use App\Enum\GradeValue;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient_person', EntityType::class, [
                'label' => 'Rodzic',
                'mapped' => false,
                'class' => User::class,
                'required' => false,
                'empty_data' => null,
                'choice_label' => 'email',
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('u')
                        ->orderBy('u.email', 'ASC')
                    ;
                },
            ])
            ->add('recipient_group', EntityType::class, [
                'label' => 'Klasa',
                'mapped' => false,
                'class' => Group::class,
                'choice_label' => 'symbol',
                'required' => false,
                'empty_data' => null,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('g')
                        ->orderBy('g.id', 'ASC')
                    ;
                },
            ])
            ->add('title', TextType::class, [
                'label' => 'Tytuł',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Treść',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Wyślij',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
