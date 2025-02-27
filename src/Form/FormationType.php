<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $builder
        ->add('titre',TextType::class, [
            'label' => 'Entrer le titre',
            'empty_data' => '',
            'attr' => [
                'class' => 'form-control',
                ]
                ])
        ->add('description',TextType::class, [
            'label' => 'Entrer la description',
            'empty_data' => '',
            'attr' => [
                'class' => 'form-control',
                ]
                ])
        ->add('difficulte', ChoiceType::class, [
            'choices' => [
                'Facile' => 'Facile',
                'Moyen' => 'Moyen',
                'Difficile' => 'Difficile',
            ],
        ])
        ->add('video', FileType::class, [
            'label' => 'Image',
            'required' => false, // Set the image field as optional
            'mapped' => false, // Important: tells Symfony not to try to map this field to an entity property
        ])  
        
    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
