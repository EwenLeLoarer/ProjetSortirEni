<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etatCreation = new Etat();
        $etatCreation->setLibelle('en création');
        $manager->persist($etatCreation);
        $this->addReference('etat-creation', $etatCreation);

        $etatOuvert = new Etat();
        $etatOuvert->setLibelle('ouvert');
        $manager->persist($etatOuvert);
        $this->addReference('etat-ouvert', $etatOuvert);

        $etatFermé = new Etat();
        $etatFermé->setLibelle('fermée');
        $manager->persist($etatFermé);
        $this->addReference('etat-fermé', $etatFermé);

        $etatEnCour = new Etat();
        $etatEnCour->setLibelle('en Cours');
        $manager->persist($etatEnCour);
        $this->addReference('etat-en-cours', $etatEnCour);

        $etatPassé = new Etat();
        $etatPassé->setLibelle('Passée');
        $manager->persist($etatPassé);
        $this->addReference('etat-passé', $etatPassé);

        $etatAnnulé = new Etat();
        $etatAnnulé->setLibelle('annulé');
        $manager->persist($etatAnnulé);
        $this->addReference('etat-annulé', $etatAnnulé);

        $etatArchivé = new Etat();
        $etatArchivé->setLibelle('archivé');
        $manager->persist($etatArchivé);
        $this->addReference('etat-archivé', $etatArchivé);

        $manager->flush();
    }
}
