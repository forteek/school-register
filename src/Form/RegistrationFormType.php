<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Adres email',
                ],
                'constraints' => [
                    new Email(message: 'Email musi być prawidłowy')
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Hasło',
                    'autocomplete' => 'new-password',
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank(message: 'Podaj hasło'),
                    new Length(
                        min: 6,
                        max: 4096,
                        minMessage: 'Hasło powinno mieć minimum {{ limit }} znaków'
                    ),
                ],
            ])
            ->add('accountType', ChoiceType::class, [
                'label' => false,
                'mapped' => false,
                'expanded' => true,
                'choices' => [
                    'Rodzic' => UserRole::PARENT,
                    'Nauczyciel' => UserRole::TEACHER,
                ],
                'constraints' => [
                    new Choice(
                        choices: [
                            UserRole::TEACHER,
                            UserRole::PARENT
                        ],
                        message: 'Wybrany typ konta musi być prawidłowy'
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
