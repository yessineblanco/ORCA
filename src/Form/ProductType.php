<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ProductName')
            ->add('ProductDescription')
            ->add('ProductPrice')
            ->add('UpdateDate')
            ->add('ProductQuantity')
            ->add('Category',EntityType::class,['class'=>Category::class,'choice_label'=>'CategoryName'])
            ->add('image',FileType::class, array('label'=>'Picture','data_class' => null,'required' => false))        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
