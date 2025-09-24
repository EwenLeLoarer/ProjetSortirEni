<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $siteChartresDeBretagne = new Site();
        $siteChartresDeBretagne->setNom('Chartres de Bretagne');
        $manager->persist($siteChartresDeBretagne);
        $this->addReference('ChartresDeBretagne', $siteChartresDeBretagne);

        $SiteNantes = new Site();
        $SiteNantes->setNom('Nantes');
        $manager->persist($SiteNantes);
        $this->addReference('SiteNantes', $SiteNantes);

        $SiteQuimper = new Site();
        $SiteQuimper->setNom('Quimper');
        $manager->persist($SiteQuimper);
        $this->addReference('Quimper', $SiteQuimper);

        $manager->flush();
    }
}
