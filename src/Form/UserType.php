<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

                $builder
                    ->add('nom', TextType::class, [
                        'label' => 'name',
                        'attr' => ['placeholder' => 'Enter your name']
                    ])
                    ->add('experiences', TextType::class, [
                        'label' => 'Experience',
                        'attr' => ['placeholder' => 'Enter your experience'],
                        'required' => false, // Makes the field optional
                    ])
                    

                    ->add('prenom', TextType::class, [
                        'label' => 'lastName',
                        'attr' => ['placeholder' => 'Enter your Last Name']
                    ])
                    ->add('email', TextType::class, [
                        'label' => 'email',
                        'attr' => ['placeholder' => 'Enter your email']
                    ])

                        // Add the new fields here
                    ->add('languages', TextType::class, [
                        'label' => 'langues',
                        'attr' => ['placeholder' => 'Enter your languages'],
                    ])
                    ->add('experiences', TextType::class, [
                        'label' => 'experience',
                        'attr' => ['placeholder' => 'Enter your experiences']
                    ])

                    
                    ->add('password', PasswordType::class, [
                        'attr' => ['placeholder' => 'Enter your password']
                    ])
                    ->add('phone', IntegerType::class, [
                        'label' => 'phone',
                        'attr' => ['placeholder' => 'Enter your phone number']
                    ])
                    ->add('photo', FileType::class, [
                        'label' => 'Image (JPG, PNG, ...)',
                        'required' => false,
                        'empty_data' => null,
                        'mapped' => false,
                        'attr' => ['accept' => 'image/*']
                    ]);
            }
        
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}


