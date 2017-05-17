<?php

namespace Jasdero\PassePlatBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class OrdersEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('catalogs')
        ->remove('comments')
        ;
    }


    public function getParent()
    {
        return OrdersType::class;
    }
}
