<?php

namespace App\Tests\Application;

use App\DataFixtures\SortieFixtures;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SortieTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = self::getContainer()
            ->get(DatabaseToolCollection::class)
            ->get(); // now it's AbstractDatabaseTool
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }
    public function testGetArchievedSortiesByNameExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => 'sortie2',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, null);

        $this->assertCount(0, $sorties);
    }
}
?>
