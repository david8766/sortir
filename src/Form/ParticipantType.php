<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo',TextType::class,['label'=>'Pseudo'])
            ->add('nom',TextType::class,['label'=>'Nom'])
            ->add('prenom',TextType::class,['label'=>'Prénom'])
            ->add('telephone',TextType::class,['label'=>'Téléphone'])
            ->add('mail',TextType::class,['label'=>'Email'])
            ->add('motDePasse',RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe ne correspond pas.',
                'options' => ['attr' => ['class' => 'password-field form-control']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répéter le mot de passe'],
            ])
            ->add('campus',EntityType::class,[
                'class' => Campus::class,
                'choice_label' => 'nom',
                'query_builder' => function (EntityRepository $repo){
                    return $repo->createQueryBuilder('c');
                },
                'label' => 'Campus',
                'attr' => [
                    'class' => 'form-select ml-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
