<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230716145037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F323DBB0D FOREIGN KEY (grille_tarifaires_id) REFERENCES grille_tarifaire (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AF86866F323DBB0D ON offre (grille_tarifaires_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F323DBB0D');
        $this->addSql('DROP INDEX UNIQ_AF86866F323DBB0D ON offre');
    }
}
