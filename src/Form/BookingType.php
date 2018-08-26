<?php
/**
 * Created by PhpStorm.
 * User: Benoit
 * Date: 01/07/2018
 * Time: 19:10
 */

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BookingType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $localtime = localtime();
        $today = ($localtime[5]+1900);
        if($localtime[4] < 10) {
            $today .= '-0' . $localtime[4];
        }else{
            $today .= '-' . $localtime[4];
        }
        $today .= '-'.$localtime[3];
        $builder
            ->add('registrationDate', DateType::class, array(
                'widget' => 'single_text',
                'label' => 'Jour de la visite',
                'attr' => array('min' => $today)
            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'placeholder' => 'Email',
                ),
                'label' => ' '
            ))
            ->add('customer', CollectionType::class, array(
                'entry_type'   => CustomerType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'label'=> 'informations visiteurs'
            ))
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}