<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('content', TextareaType::class, [
                'empty_data' => ''
            ])
            ->add('duration')
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestampsAndSlug(...))
        ;
    }

    public function attachTimestampsAndSlug(PostSubmitEvent $event): void
    {
        $data = $event->getData();
        if (!($data instanceof Recipe)) {
            return;
        }

        if (!$data->getId()) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }

        $data->setUpdatedAt(new \DateTimeImmutable());

        $slugger = new AsciiSlugger();
        $slug = strtolower($slugger->slug($data->getTitle()));

        $data->setSlug($slug);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
