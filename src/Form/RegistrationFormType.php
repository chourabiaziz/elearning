<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $hidden = $options['hidden_languages'] ?? false;

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Name',
                'attr' => ['placeholder' => 'Enter your name'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your name']),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Last Name',
                'attr' => ['placeholder' => 'Enter your last name'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your last name']),
                ],
            ])


            ->add('languages', TextType::class, [
                'label' => 'langue', // The label text remains
                'label_attr' => [
                    'style' => 'display: none;', // Hides the label using CSS
                ],
                'required' => false, // No longer required
            ])
            
            ->add('experiences', TextType::class, [
                'label' => 'experience', // The label text remains
                'label_attr' => [
                    'style' => 'display: none;', // Hides the label using CSS
                ],
                'required' => false, // No longer required
            ])
            
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Enter your email'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your email']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Enter your password'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a password']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters long.',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[\W_]).+$/',
                        'message' => 'Your password must contain at least one uppercase letter and one special character.',
                    ]),
                ],
            ])
            ->add('phone', IntegerType::class, [
                'label' => 'Phone',
                'attr' => ['placeholder' => 'Enter your phone number'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your phone number']),
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Image (JPG, PNG, ...)',
                'required' => false, // Le champ n'est pas obligatoire
                'empty_data' => null, // Si aucun fichier n'est téléchargé, ce champ reste à null
                'mapped' => false, // Ce champ n'est pas mappé directement sur l'entité
                'attr' => ['accept' => 'image/*'],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'You should agree to our terms.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'hidden_languages' => false, // Default to false (field enabled)

        ]);
    }
}