<?php

namespace App\form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('prenom', TextType::class, [
            'required' => true,
            'label' => "Prénom",
            'attr' => [
                'placeholder' => "Prénom"
            ]])

            ->add('nom', TextType::class, [
                'required' => true,
                'label' => "Nom",
                'attr' => [
                    'placeholder' => "Nom"
                ]])

            ->add('email', EmailType::class, [
                'required' => true,
                'label' => "Email",
                'attr' => [
                    'placeholder' => "Email"
                ]])

            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => "Mot de passe",
                'attr' => [
                    'placeholder' => "Mot de passe"
                ]])

            ->add('submit', SubmitType::class, [
                'label' => 'Publier mon Article'
            ])

            ->getForm()
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membre::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'form';
    }


}