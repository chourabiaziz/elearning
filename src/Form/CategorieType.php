<?php
namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;



class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('nom', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom de la catégorie ne doit pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'Le nom doit comporter au moins 3 caractères.',
                ]),
                new Assert\Regex([
                    'pattern' => '/^[A-Z]/',
                    'message' => 'Le nom doit commencer par une majuscule.',
                ]),
            ],
        ])
            ->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description ne peut pas être vide.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z]/',
                        'message' => 'La description doit commencer par une lettre.',
                    ]),
                ],
            ])
            ->add('dateCreated', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Sélectionnez la date de création'],
                'constraints' => [
                    new NotBlank(['message' => 'La date de création est requise.']),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // Ce champ n'est pas mappé directement à l'entité
                'required' => false, // Facultatif selon les besoins
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image au format JPG ou PNG.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
