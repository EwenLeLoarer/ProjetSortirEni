<?php

namespace App\DataFixtures;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Couchbase\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $site = new Site();
        $site->setNom('site intergalactic');
        $manager->persist($site);

        $userBasic = new Utilisateur();
        $userBasic->setEmail('basicuser@gmail.com');
        $userBasic->setRoles(['ROLE_USER']);

        // hash the password properly
        $hashedPassword = $this->passwordHasher->hashPassword($userBasic, 'password');
        $userBasic->setPassword($hashedPassword);
        $userBasic->setNom('Doe');
        $userBasic->setPrenom('John');
        $userBasic->setTelephone('0101010101');
        $userBasic->setSite($site);
        $userBasic->setIsActif(true);
        $userBasic->setPseudo('testUser');
        $userBasic->setPhoto('');
        // persist the user
        $manager->persist($userBasic);
        $this->addReference('user-basic', $userBasic);

        $userAdmin = new Utilisateur();
        $userAdmin->setEmail('adminuser@gmail.com');
        $userAdmin->setRoles(['ROLE_ADMIN']);

        // hash the password properly
        $hashedPassword = $this->passwordHasher->hashPassword($userAdmin, 'passwordAdmin');
        $userAdmin->setPassword($hashedPassword);
        $userAdmin->setNom('Done');
        $userAdmin->setPrenom('Johnathan');
        $userAdmin->setTelephone('0202020202');
        $userAdmin->setSite($site);
        $userAdmin->setIsActif(true);
        $userAdmin->setPseudo('testAdmin');
        $userAdmin->setPhoto('');
        // persist the user
        $manager->persist($userAdmin);
        $this->addReference('user-admin', $userAdmin);

        // flush changes
        $manager->flush();
    }
}
