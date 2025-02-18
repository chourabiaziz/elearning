<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Quiz;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('image', FileType::class, [
            'label' => 'Entrer votre image', // Modifier ici
            'mapped' => false,
            'empty_data' => '',
            'required' => false,

            'attr' => [
                'class' => 'form-control', 
                'data_class' => null,  // This prevents Symfony from expecting a File object

            ]
        ])
        ->add('incorrect1',TextType::class, [
            'label' => 'Entrer votre réponse incorrecte 1',
            'empty_data' => '',
            'attr' => [
                'class' => 'form-control',
                ]
                ])
                

            ->add('incorrect2',TextType::class, [
                'label' => 'Entrer votre réponse incorrect 2',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                    ]
                    ])
            ->add('correct',TextType::class, [
                'label' => 'Entrer votre réponse correct',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                    ]
                    ])
             
        ;
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
