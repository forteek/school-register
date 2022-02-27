<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\Student;
use App\Enum\GradeValue;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeAssignmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('student', EntityType::class, [
                'label' => 'Uczeń',
                'class' => Student::class,
                'choice_label' => function (Student $student) {
                    return $student->getFirstName() . ' ' . $student->getLastName();
                },
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er
                        ->createQueryBuilder('s')
                        ->where('s.group = :group_id')
                        ->setParameter('group_id', $options['group_id'])
                        ->orderBy('s.lastName', 'ASC')
                        ->addOrderBy('s.firstName', 'DESC')
                    ;
                },
            ])
            ->add('value', ChoiceType::class, [
                'label' => 'Ocena',
                'choices' => GradeValue::ALL,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Oceń',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grade::class,
            'group_id' => null
        ]);
    }
}
