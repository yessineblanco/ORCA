<?php

namespace App\Form;

use App\Entity\Traitement;
use App\Entity\Maladie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class TraitementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NomTraitement')
            ->add('TypeTraitement')
            ->add('DateDebutTraitement')
            ->add('DateFinTraitement')
            ->add('Description')
            ->add('maladie',EntityType::class,[
                'class'=>Maladie::class,
                'choice_label'=>'NomMaladie',
                'multiple'=>false,
                'expanded'=>false
            ]);
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Traitement::class,
        ]);
    }
}
