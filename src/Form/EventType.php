<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Enter event name'],
            ])
            ->add('dateEvent', DateType::class, [
                'label' => 'Date Evenement',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Select event date'],
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Localisation',
                'attr' => ['placeholder' => 'Enter location'],
            ])
            ->add('seuil', IntegerType::class, [
                'label' => 'Seuil',
                'attr' => ['min' => 1, 'placeholder' => 'Enter threshold'],
            ])
            ->add('imageE', FileType::class, [
                'label' => 'Image (JPG, PNG, ...)',
                'required' => false, // Le champ n'est pas obligatoire
                'empty_data' => null, // Si aucun fichier n'est téléchargé, ce champ reste à null
                'mapped' => false, // Ce champ n'est pas mappé directement sur l'entité
                'attr' => ['accept' => 'image/*'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
