<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Quantity;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use App\Form\QuantityType;

class RecipeType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'empty_data' => '',
                'required' => false
            ])
            ->add('category', CategoryAutocompleteField::class)
            ->add('content', TextareaType::class, [
                'empty_data' => ''
            ])
            ->add('duration')
            ->add('thumbnailFile', FileType::class, [
                'required' =>false
            ])
            ->add('quantities', CollectionType::class, [
                'entry_type' => QuantityType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' =>[
                    // 'data-controller' => 'form-collection',
                    'data-form-collection-add-label-value' => 'Ajouter un ingrédient',
                    'data-form-collection-delete-label-value' => 'Supprimer l\'ingrédient',
                    ]
                ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->attachTimestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
