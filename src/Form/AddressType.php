<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('city', TextType::class,[
            'label' => 'Ville: ',
            'required' => true
        ])
        ->add('street',TextType::class,[
            'label' => 'N° de voie et rue: ',
            'required' => true
        ] )
        ->add('zipCode', TextType::class,[
            'label' => 'Code postale: ',
            'required' => true
        ] )
        ->add('supplement', TextType::class,[
            'label' => 'Complément( interphone, étage, etc... )',
            'required' => true
        ] )
        ->add('send', SubmitType::class, [
                'label' => "Envoyer",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
