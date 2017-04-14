<?php

namespace Jasdero\PassePlatBundle\Form\Type;

use Jasdero\PassePlatBundle\Entity\Category;
use Jasdero\PassePlatBundle\Entity\Vat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CatalogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('pretaxPrice', NumberType::class, array(
                'label' => 'Pretax-price (optional)',
                'required' => false,
            ))
            ->add('activated', ChoiceType::class, array(
                'label' => false,
                'choices' =>
                    ['Enabled' => 1,
                        'Disabled' => 0],
            ))
            ->add('branch', EntityType::class, array(
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Branch (optional)',
                'required' => false
            ))
            ->add('category', EntityType::class, array(
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Category (optional)',
                'required' => false
            ))
            ->add('subCategory', EntityType::class, array(
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'subCategory (optional)',
                'required' => false
            ))
            ->add('vat', EntityType::class, array(
                'class' => Vat::class,
                'choice_label' => 'rate',
                'label' => 'rate (optional)',
                'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jasdero\PassePlatBundle\Entity\Catalog'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'jasderopasseplatbundle_catalog';
    }


}
