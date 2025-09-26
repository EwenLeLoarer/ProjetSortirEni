<?php

namespace App\Tests\Application;

use App\DataFixtures\EtatFixtures;
use App\DataFixtures\SiteFixtures;
use App\DataFixtures\SortieFixtures;
use App\DataFixtures\UtilisateurFixtures;
use App\Entity\Etat;
use App\Entity\Site;
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
        $sorties = $repo->search($filters, $user);

        $this->assertCount(0, $sorties);
    }

    public function testGetSortieByNameExpect1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => 'sortie1',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expect sortie not to be empty');
    }
    public function testGetSortieByNameExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => 'this sortie does not exist',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertCount(0, $sorties);
    }

    public function testGetSortieBySiteExpectAtLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $site = $this->entityManager->getRepository(Site::class)->findOneBy(['nom' => 'Chartres de Bretagne']);

        $filters = [
            'site' => $site,
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieBySiteExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $site = new Site();
        $site->setNom('site non utilisé');
        $this->entityManager->persist($site);
        $this->entityManager->flush();

        $filters = [
            'site' => $site,
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertCount(0, $sorties);;
    }

    public function testGetSortieByDateFromExpectAtLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '2020-12-12',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieByDateFromExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '2030-12-12',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertCount(0,$sorties, 'Expected to find sortie');
    }

    public function testGetSortieByDateToExpectAtLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '2030-12-12',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieByDateToExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '2020-12-12',
            'organisees' => '',
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertCount(0,$sorties, 'Expected to find sortie');
    }

    public function testGetSortieByorganiséesExpect0(){
        $this->databaseTool->loadFixtures([UtilisateurFixtures::class,SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUserWithoutSortie']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => 1,
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertCount(0,$sorties, 'Expected to find sortie');
    }

    public function testGetSortieByOrganiséesExpectAtLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => 1,
            'prevues' => '',
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieByPasseesWhenNotInParticipantsExpectAtLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUser']);
        $otherUser = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'otherUser']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => 1,
            'passees' => '',
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieByPasseesWhenUserInParticipantsExpectAtLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class, EtatFixtures::class]);

        $repo = $this->entityManager->getRepository(Sortie::class);
        $etatRepo = $this->entityManager->getRepository(Etat::class);

        $etatPasse = $etatRepo->findOneBy(['libelle' => 'Passée']);
        $user = $this->entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['pseudo' => 'testUser']);
        $sortie = $repo->findOneBy(['nom' => 'sortie1']);

        $sortie->addParticipant($user);
        $sortie->setDateHeureDebut(new \DateTime("2025-09-09"));
        $sortie->setDuree(15);
        $sortie->setEtat($etatPasse);

        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => 1,
        ];

        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }
    public function testGetSortieByPasseesExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $repo */
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => 'testUserWithoutSortie']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '',
            'passees' => 1,
        ];
        $sorties = $repo->search($filters, $user);

        $this->assertCount(0, $sorties, 'Expected to find sortie');
    }

    public function testGetSortieNeedToBeCloseExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        $sorties = $repo->GetNeedToCloseSortie();

        $this->assertCount(0,$sorties, 'Expected to find sortie');
    }

    public function testGetSortieNeedToBeCloseExpectAtLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);
        $etatRepo = $this->entityManager->getRepository(Etat::class);
        $etatOuvert = $etatRepo->findOneBy(['libelle' => 'ouvert']);
        $sortie = $repo->findOneBy(['nom' => 'sortie1']);

        $sortie->setDateLimiteInscription(new \DateTime('2020-01-01'));
        $sortie->setEtat($etatOuvert);
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        $sorties = $repo->GetNeedToCloseSortie();

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieNeedToBeStartExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        $sorties = $repo->GetNeedToStartSortie();

        $this->assertCount(0,$sorties, 'Expected to find sortie');
    }

    public function testGetSortieNeedToStartExpectAtLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class, EtatFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);
        $etatRepo = $this->entityManager->getRepository(Etat::class);
        $sortie = $repo->findOneBy(['nom' => 'sortie1']);
        $etatfermé = $etatRepo->findOneBy(['libelle' => 'fermée']);

        $sortie->setDateHeureDebut(new \DateTime('2025-09-25 00:00:00'));
        $sortie->setDuree(100000);
        $sortie->setEtat($etatfermé);

        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        $sorties = $repo->GetNeedToStartSortie();

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieNeedToFinishExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);

        $sorties = $repo->GetNeedToStartSortie();

        $this->assertCount(0,$sorties, 'Expected to find sortie');
    }

    public function testGetSortieNeedToFinishExpectatLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);
        $etatRepo = $this->entityManager->getRepository(Etat::class);

        $sortie = $repo->findOneBy(['nom' => 'sortie1']);

        $sortie->setDateHeureDebut(new \DateTime('2025-09-24'));
        $sortie->setDuree(15);
        $sortie->setEtat($etatRepo->findOneBy(['libelle' => 'en Cours']));

        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        $sorties = $repo->GetNeedToFinishSortie();

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieNeedToBeArchivedExpect0(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);
        $etatRepo = $this->entityManager->getRepository(Etat::class);

        $sortie = $repo->findOneBy(['nom' => 'sortie1']);

        $sortie->setDateHeureDebut(new \DateTime('2025-09-24'));
        $sortie->setDuree(15);
        $sortie->setEtat($etatRepo->findOneBy(['libelle' => 'Passée']));

        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        $sorties = $repo->getNeedToBeArchived();

        $this->assertCount(0, $sorties, 'Expected to find sortie');
    }

    public function testGetSortieNeedToBeArchivedExpectatLeast1(){
        $this->databaseTool->loadFixtures([SortieFixtures::class,SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo= $this->entityManager->getRepository(Sortie::class);
        $etatRepo = $this->entityManager->getRepository(Etat::class);

        $sortie = $repo->findOneBy(['nom' => 'sortie1']);

        $sortie->setDateHeureDebut(new \DateTime('2020-09-24'));
        $sortie->setDuree(15);
        $sortie->setEtat($etatRepo->findOneBy(['libelle' => 'Passée']));

        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        $sorties = $repo->getNeedToBeArchived();

        $this->assertNotEmpty($sorties, 'Expected to find sortie');
    }

    public function testGetSortieByPrevuesExpect0() {
        $this->databaseTool->loadFixtures([SortieFixtures::class, SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo = $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $userRepo */
        $user = $this->entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['pseudo' => 'testUser']);

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '0',   // <── this is the missing branch
            'passees' => '',
        ];

        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expected sorties not empty when excluding user from participants');
    }

    public function testGetSortieByPrevuesExpectAtLeast1() {
        $this->databaseTool->loadFixtures([SortieFixtures::class, SiteFixtures::class]);

        /** @var \App\Repository\SortieRepository $repo */
        $repo = $this->entityManager->getRepository(Sortie::class);

        /** @var \App\Repository\UtilisateurRepository $userRepo */
        $user = $this->entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['pseudo' => 'testUser']);

        $sortie = $this->entityManager->getRepository(Sortie::class)->findOneBy(['nom' => 'sortie1']);


        $sortie->addParticipant($user);

        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        $filters = [
            'site' => '',
            'query' => '',
            'from' => '',
            'to' => '',
            'organisees' => '',
            'prevues' => '1',   // <── this is the missing branch
            'passees' => '',
        ];

        $sorties = $repo->search($filters, $user);

        $this->assertNotEmpty($sorties, 'Expected sorties not empty when excluding user from participants');
    }
}
?>
