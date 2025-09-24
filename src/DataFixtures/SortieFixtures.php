<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $etatCreation = $this->getReference('etat-creation', Etat::class);
        $etatOuvert = $this->getReference('etat-ouvert', Etat::class);
        $etatFerme = $this->getReference('etat-fermé', Etat::class);
        $etatEnCours = $this->getReference('etat-en-cours', Etat::class);
        $etatPassé = $this->getReference('etat-passé', Etat::class);
        $etatAnnulé = $this->getReference('etat-annulé', Etat::class);
        $etatArchivé = $this->getReference('etat-archivé', Etat::class);

        $userAdmin = $this->getReference('user-admin', Utilisateur::class);
        $userBasic = $this->getReference('user-basic', Utilisateur::class);

        $chartresDeBretagne = $this->getReference('ChartresDeBretagne', Site::class);
        $siteNantes = $this->getReference('SiteNantes', Site::class);

        $Rennes = $this->getReference('Rennes', Ville::class);
        $Nantes = $this->getReference('Nantes', Ville::class);

        $gazoline = $this->getReference('Gazoline', Lieu::class);
        $wareHouse = $this->getReference('WareHouse', Lieu::class);

        $sortie1 = new Sortie();
        $sortie1->setNom('sortie1');
        $sortie1->setDateHeureDebut(new \DateTime('2030-01-01'));
        $sortie1->setDuree(1500);
        $sortie1->setDateLimiteInscription(new \DateTime('2025-12-12'));
        $sortie1->setNbInscriptionsMax(40);
        $sortie1->setInfosSortie('bleh');
        $sortie1->setEtat($etatCreation);
        $sortie1->setOrganisateur($userBasic);
        $sortie1->setsite($chartresDeBretagne);
        $sortie1->setLieu($gazoline);
        $sortie1->setAnnulationMotif('');
        $manager->persist($sortie1);

        $sortieArchived = new Sortie();
        $sortieArchived->setNom('sortie2');
        $sortieArchived->setDateHeureDebut(new \DateTime('2024-01-01'));
        $sortieArchived->setDuree(1500);
        $sortieArchived->setDateLimiteInscription(new \DateTime('2024-12-12'));
        $sortieArchived->setNbInscriptionsMax(40);
        $sortieArchived->setInfosSortie('bleh');
        $sortieArchived->setEtat($etatArchivé);
        $sortieArchived->setOrganisateur($userAdmin);
        $sortieArchived->setsite($siteNantes);
        $sortieArchived->setLieu($gazoline);
        $sortieArchived->setAnnulationMotif('');
        $manager->persist($sortieArchived);


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
          EtatFixtures::class,
          UtilisateurFixtures::class,
          SiteFixtures::class,
          LieuFixtures::class,
        ];
    }
}
