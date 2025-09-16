<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916073622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD is_administrateur TINYINT(1) NOT NULL, ADD is_actif TINYINT(1) NOT NULL, DROP mail, DROP administrateur, DROP actif');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD mail VARCHAR(255) NOT NULL, ADD administrateur TINYINT(1) NOT NULL, ADD actif TINYINT(1) NOT NULL, DROP is_administrateur, DROP is_actif');
    }
}
