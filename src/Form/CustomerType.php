<?php
namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\ORM\EntityManagerInterface;

class CustomerType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, array(
                'label'  => ' ',
                'attr' => array(
                    'placeholder' => 'Nom',
                )
            ))
            ->add('firstName', TextType::class, array(
                'label'  => ' ',
                'attr' => array(
                    'placeholder' => 'Prénom',
                )
            ))
            ->add('country', CountryType::class, array(
                'label'  => ' ',
                'placeholder' => 'France'
            ))
            ->add('birthDate', DateType::class, array(
                'label'  => 'Date de naissance ',
                'help' => 'Entrez votre date de naissance, défini votre tarif',
                'widget' => 'single_text',
            ))
            ->add('reduced', CheckboxType::class, array(
                'label'    => 'Tarif réduit',
                'required' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}