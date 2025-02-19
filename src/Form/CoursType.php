<?php
// src/Form/CoursType.php
namespace App\Form;

use App\Entity\Cours;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du cours',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrer le titre du cours', // Définition du placeholder ici dans 'attr'
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit comporter au moins {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z][a-zA-Z0-9\s]*$/',
                        'message' => 'Le titre doit commencer par une majuscule et ne contenir que des lettres, des chiffres et des espaces.',
                    ]),
                ],
            ])
            ->add('contenu', TextType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrer le contenu', // Définition du placeholder ici dans 'attr'
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Le contenu doit comporter au moins {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z][a-zA-Z0-9\s]*$/',
                        'message' => 'Le contenu doit commencer par une majuscule et ne contenir que des lettres, des chiffres et des espaces.',
                    ]),
                ],
            ])
            ->add('dateCreated', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de création',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG ou PNG.',
                    ]),
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La catégorie est obligatoire.',
                    ]),
                ],
            ])
            
            ->add('video', FileType::class, [
                'label' => 'Vidéo (MP4)',
                'required' => false,
                'mapped' => false,
                'attr' => ['accept' => 'video/mp4'],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['video/mp4'],
                        'mimeTypesMessage' => 'Veuillez télécharger une vidéo au format MP4.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
