<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints as Assert;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('email', options:[
                'label' => 'Email',
            ])
           ->add('roles', ChoiceType::class, [
               'choices'  => [
                   'Admin' => 'ROLE_ADMIN',
                   'User' => 'ROLE_USER',
               ],
               'multiple' => true,
           ])
            ->add('password', PasswordType::Class, options:[
                'label' => 'Mot de passe',
                'required'=> false,
                'mapped' => false,
                /*'constraints' => [
                    new Assert\PasswordStrength([
                        'minScore' => 1,
                        'message' => 'Le mot de passe n\'est pas suffisamment sécurisé.',
                    ]),
                ],*/
            ])
            ->add('passwordConfirmation', PasswordType::Class, options:[
                'label' => 'Confirmation',
                'mapped' => false,
                'required'=> false,
            ],
                )
            ->add('nom', options: [
                'label' => 'Nom',
            ])
            ->add('prenom', options: [
                'label' => 'Prénom',
            ])
            ->add('pseudo', options: [
                'label' => 'Pseudo',
            ])
            ->add('telephone', options: [
                'label' => 'Téléphone',
            ])
            ->add('isActif', options:[
                'label' => 'Actif',
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Site de rattachement',
            ])
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image (jpg, png)',
                    ])
                ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'utilisateur',
        ]);
    }
}
