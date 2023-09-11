<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230716153137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grille_tarifaire ADD offre_id INT NOT NULL');
        $this->addSql('ALTER TABLE grille_tarifaire ADD CONSTRAINT FK_63E2418B4CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_63E2418B4CC8505A ON grille_tarifaire (offre_id)');
        $this->addSql('ALTER TABLE offre CHANGE grille_tarifaires_id grille_tarifaire_id INT NOT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F2C47CC22 FOREIGN KEY (grille_tarifaire_id) REFERENCES grille_tarifaire (id)');
        $this->addSql('CREATE INDEX IDX_AF86866F2C47CC22 ON offre (grille_tarifaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F2C47CC22');
        $this->addSql('DROP INDEX IDX_AF86866F2C47CC22 ON offre');
        $this->addSql('ALTER TABLE offre CHANGE grille_tarifaire_id grille_tarifaires_id INT NOT NULL');
        $this->addSql('ALTER TABLE grille_tarifaire DROP FOREIGN KEY FK_63E2418B4CC8505A');
        $this->addSql('DROP INDEX UNIQ_63E2418B4CC8505A ON grille_tarifaire');
        $this->addSql('ALTER TABLE grille_tarifaire DROP offre_id');
    }
}
