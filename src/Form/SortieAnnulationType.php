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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieAnnulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'disabled' => true,
                'label' => 'Nom de la sortie',
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'disabled' => true,
                'label' => 'Date de la sortie',
                'widget' => 'single_text',
                'html5' => true,])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'label' => 'Ville organisatrice',
                'choice_label' => 'nom',
                'disabled' => true,
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'disabled' => true,
                'label' => 'Lieu',
            ])
            ->add('annulationMotif', TextAreaType::class, [
                'mapped' => true,
                'required' => true,
                'label' => 'Motif de l\'annulation',
            ])
            ->add('Enregistrer', SubmitType::class, [
                'label' => 'Supprimer la sortie',
                'attr' => ['class' => 'button button_primary cancel'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
