<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', options: [
                'label' => 'Nom de la sortie',
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('nbInscriptionsMax', options: [
                'label' => 'Nombre de places',
            ])
            ->add('duree', options: [
                'label' => 'Durée',
            ])
            ->add('infosSortie', options: [
                'label' => 'Description et infos',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'label' => 'Lieu',
                'attr' => [
                    'id' => 'sortie_lieu'
                ],
            ])

            ->add('rue', null, [
                'mapped' => false,
                'disabled' => true,
                'label' => 'Rue',
                'attr' => ['id' => 'sortie_rue'],
            ])
            ->add('codePostal', null, [
                'mapped' => false,
                'disabled' => true,
                'label' => 'Code postal',
                'attr' => ['id' => 'sortie_codePostal'],
            ])
            ->add('ville', null, [
                'mapped' => false,
                'disabled' => true,
                'label' => 'Ville',
                'attr' => ['id' => 'sortie_ville'],
            ])
            ->add('latitude', null, [
                'mapped' => false,
                'disabled' => true,
                'label' => 'Latitude',
                'attr' => ['id' => 'sortie_latitude'],
            ])
            ->add('longitude', null, [
                'mapped' => false,
                'disabled' => true,
                'label' => 'Longitude',
                'attr' => ['id' => 'sortie_longitude'],
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Ville organisatrice',
            ])
            ->add('organisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => function(Utilisateur $utilisateur) {
                    return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                },
                'mapped' => false,
                'disabled' => true,
                'data' => $options['organisateur'],
            ])
            ->add('participants', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => function(Utilisateur $utilisateur) {
                    return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('enregistrer', SubmitType::class, [
                'attr' => ['class' => 'button button_primary'],
                ])
            ->add('publier', SubmitType::class,
                ['label' => 'Publier la sortie',
                'attr' => ['class' => 'button button_primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'organisateur' => null,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'utilisateur',
        ]);
    }
}
