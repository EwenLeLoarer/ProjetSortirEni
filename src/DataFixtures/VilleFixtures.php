<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $Rennes = new Ville();
        $Rennes->setNom('Rennes');
        $Rennes->setCodePostal('35000');
        $manager->persist($Rennes);
        $this->addReference('Rennes', $Rennes);

        $Nantes = new Ville();
        $Nantes->setNom('Nantes');
        $Nantes->setCodePostal('44000');
        $manager->persist($Nantes);
        $this->addReference('Nantes', $Nantes);

        $Paris = new Ville();
        $Paris->setNom('Paris');
        $Paris->setCodePostal('75000');
        $manager->persist($Paris);
        $this->addReference('Paris', $Paris);

        $manager->flush();
    }
}
