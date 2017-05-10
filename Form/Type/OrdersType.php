<?php

namespace Jasdero\PassePlatBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                'choice_label' => function (Catalog $catalog) {
                    $name = $catalog->getName();
                    $branch = $catalog->getBranch();
                    $category = $catalog->getCategory();
                    $subCategory = $catalog->getSubCategory();
                    $metas = [];
                    $show = "";
                    if($branch){
                        array_push($metas, $branch->getName());
                    }
                    if($category){
                        array_push($metas,$category->getName());
                    }
                    if($subCategory){
                        array_push($metas, $subCategory->getName());
                    }
                    foreach ($metas as $meta) {
                        $show .= $meta."/ ";
                    }
                    return $name." Category : ".$show;
                },
                'expanded' => true,
                'multiple' => true,
                'mapped' => false,
            ))
            ->add('user', EntityType::class, array(
                'class' => 'Jasdero\PassePlatBundle\Entity\User',
                'choice_label' => 'username',
                'label' => 'Owner',
                'mapped' => 'false'
            ))
            ->add('comments',CollectionType::class, array(
                'entry_type'=> CommentType::class,
                'required' => false,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jasdero\PassePlatBundle\Entity\Orders',
            'cascade_validation' => true
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
