<?php

namespace Jasdero\PassePlatBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Jasdero\PassePlatBundle\Entity\Catalog;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class OrdersType extends AbstractType
{
    /**
     * Type used create an order; only displays catalog entries which are activated and user associated with order
     * {@inheritdoc}
     */


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('catalogs', EntityType::class, array(
            'class' => 'Jasdero\PassePlatBundle\Entity\Catalog',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.activated = true');
            },
            'choice_label' => function (Catalog $product) {
                return $product->getDescription() . ' Pre-tax price : ' . $product->getPretaxPrice() . '€';
            },
            'label' => 'Products',
            'expanded' => true,
            'multiple' => true,
            'mapped' => false,
        ))
            ->add('user', EntityType::class, array(
                'class' => 'Jasdero\PassePlatBundle\Entity\User',
                'choice_label' => 'username',
                'label' => 'Order owner',
                'mapped' => 'false'
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jasdero\PassePlatBundle\Entity\Orders'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'jasderopasseplatbundle_orders';
    }


}
