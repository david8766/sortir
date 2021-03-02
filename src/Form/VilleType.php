<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
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

            ->add('code_postal', TextType::class, [
                'label'=>'Code postal',
                'required' => true,
                'attr'=>['placeholder'=>'Indiquez le code postal']
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
