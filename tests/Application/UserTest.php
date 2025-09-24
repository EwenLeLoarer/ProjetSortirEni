<?php

namespace App\tests\Application;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserTest extends WebTestCase
{
    public function testLoginSuccesUser():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'testuser',
            '_password' => 'password',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testLoginSuccessAdmin():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'testAdmin',
            '_password' => 'passwordAdmin',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testLoginFailusernameUser():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'testusersdfdsfddf',
            '_password' => 'password',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
    }

    public function testLoginFailpasswordUser():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'testuser',
            '_password' => 'passwordfgfdfgfdg',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
    }

    public function testLogoutSuccessUser():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'testuser',
            '_password' => 'password',
        ]);
        $client->submit($form);

        $crawler = $client->followRedirect();

        $link = $crawler->selectLink('Se déconnecter')->link();
        $client->click($link);

        $this->assertResponseRedirects('/');
    }

    public function testLogoutAdmin():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'testAdmin',
            '_password' => 'passwordAdmin',
        ]);
        $client->submit($form);

        $crawler = $client->followRedirect();

        $link = $crawler->selectLink('Se déconnecter')->link();
        $client->click($link);

        $this->assertResponseRedirects('/');
    }
}
?>
