<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Get Ville references created in VilleFixtures
        $rennes = $this->getReference('Rennes', Ville::class);
        $gazoline = new Lieu();
        $gazoline->setVille($rennes);
        $gazoline->setNom('Gazoline');
        $gazoline->setRue('24 rue Nantaise');
        $gazoline->setLatitude(48.11233);
        $gazoline->setLongitude(-1.6853);
        $manager->persist($gazoline);
        $this->addReference('Gazoline', $gazoline);

        $nantes = $this->getReference('Nantes', Ville::class);
        $warehouse = new Lieu();
        $warehouse->setVille($nantes);
        $warehouse->setNom('WareHouse');
        $warehouse->setRue('21 quais des antilles');
        $warehouse->setLatitude(47.20139);
        $warehouse->setLongitude(-1.57285);
        $manager->persist($warehouse);
        $this->addReference('WareHouse', $warehouse);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            VilleFixtures::class,
        ];
    }
}
