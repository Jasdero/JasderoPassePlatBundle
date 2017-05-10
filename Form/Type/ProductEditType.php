<?php

namespace Jasdero\PassePlatBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Jasdero\PassePlatBundle\Entity\Catalog;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    //Type used by the admin to edit a product line; only displays status entries which are activated
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('catalog', EntityType::class, array(
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
                'multiple' => false,
            ))
            ->add('pretaxPrice', NumberType::class)
            ->add('vatRate', NumberType::class, array(
                'required' => false,
                'label' => 'Vat Rate (optional)'
            ))
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
