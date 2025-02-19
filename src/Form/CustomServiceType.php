<?php
// src/Form/CustomServiceType.php
namespace App\Form;

use App\Entity\CustomService;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('niveau', ChoiceType::class, [
            'choices' => [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,
                '6' => 6,
                '7' => 7,
                '8' => 8,
                '9' => 9,
                '10' => 10,
            ],
            'expanded' => false, // Liste déroulante classique
            'multiple' => false, // Un seul choix possible
            'label' => 'Choisissez le niveau sur 10 ',
        ])
        
            ->add('questions', TextType::class, [
                'label' => 'Quelle matière souhaitez-vous perfectionner avec notre aide ? '
            ])
            ->add('plan', TextType::class, [
                'label' => 'But à achever'
            ])
            ->add('image', FileType::class, [
                'label' => 'Problèmes rencontrés',
                'required' => false, // optionnel
                'mapped' => false, // ne mappe pas directement l'entité
            ])
            ->add('Categorie', ChoiceType::class, [
                'choices' => $options['categories'],
                'label' => 'Catégorie'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomService::class,
            'categories' => [], // Ajouter une option pour passer les catégories
        ]);
    }
}
