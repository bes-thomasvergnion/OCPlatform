<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OC\PlatformBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',     TextType::class)
            ->add('author',    TextType::class)
            ->add('content',   TextareaType::class)
            ->add('save',      SubmitType::class)
            ->add('categories', EntityType::class, array(
                'class'    => 'OCPlatformBundle:Category',
                'choice_label' => 'name',
                'multiple' => true,
              ))
            ->add('image',     ImageType::class, array('required' => false))       
        ;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, 
            function(FormEvent $event) {
                $advert = $event->getData();
                if (null === $advert) {
                return;
                }
                if (!$advert->getPublished() || null === $advert->getId()) {
                    $event->getForm()->add('published', CheckboxType::class, array('required' => false));
                }
                else {        
                    $event->getForm()->remove('published');
                }
            }
        );
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'OC\PlatformBundle\Entity\Advert'
      ));
    }

    public function getName()
    {
      return 'oc_platformbundle_advert';
    }
}
