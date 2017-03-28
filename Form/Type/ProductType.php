<?php

namespace Jasdero\PassePlatBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    //Type used by the admin to edit a product line; only displays status entries which are activated
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pretaxPrice')
            ->add('vatRate')
            ->add('state', EntityType::class, array(
                'class'=>'Jasdero\PassePlatBundle\Entity\State',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.activated = true');
                },
                'choice_label' => 'name',
                'expanded'=>false,
                'multiple'=>false,
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jasdero\PassePlatBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'jasderopasseplatbundle_product';
    }


}
