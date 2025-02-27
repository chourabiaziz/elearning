<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('image', FileType::class, [
            'label' => 'Entrer votre image', // Modifier ici
            'mapped' => true,
            'empty_data' => null,    
            'attr' => [
                'class' => 'form-control', 
                'data_class' => "",  // This prevents Symfony from expecting a File object

            ]
        ])
            ->add('titre', TypeTextType::class, [
                'label' => 'Entrer Titre',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])->add('contenu', TypeTextType::class, [
                'label' => 'Entrer le contenu',
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
            'data_class' => Post::class,
        ]);
    }
}
