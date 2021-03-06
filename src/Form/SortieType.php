<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom',
                'required' => true,
                'attr'=>['placeholder'=>'Indiquez un nom',
                    'autofocus' => true]
            ])

            ->add('dateHeureDebut', DateTimeType::class, [
                'label'=>'Date et heure de début',
                'required' => true,
                'date_widget' => 'single_text',
                'html5' => true,
                'time_widget' => 'single_text',
            ])

            ->add('duree', IntegerType::class, [
                'label'=>'Durée',
                'required' => true,
                'attr'=>['placeholder'=>'En minutes']
            ])
            ->add('dateCloture', DateType::class, [
                'label'=>'Clôture des inscriptions',
                'required' => true,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label'=>'Nombre de places',
                'required' => true,
                'attr'=>['placeholder'=>'Nombre maxi de participants']
            ])

            ->add('description', TextareaType::class, [
                'label'=>'Description',
                'required' => true,
                'attr'=>['placeholder'=>'Décrivez les activités']
            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un campus',
            ])
            /*
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une ville',
            ])
            */
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nomComplet',
                'placeholder' => 'Sélectionnez un lieu',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}