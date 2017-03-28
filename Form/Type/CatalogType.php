<?php

namespace Jasdero\PassePlatBundle\Form\Type;

use Jasdero\PassePlatBundle\Entity\Vat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        $builder->add('name')
            ->add('description')
            ->add('pretaxPrice')
            ->add('activated', ChoiceType::class, array(
                'choices' =>
                    ['Enabled' => 1,
                        'Disabled' => 0],
            ))
            ->add('vat', EntityType::class, array(
                'class' => Vat::class,
                'choice_label' => 'rate',
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
