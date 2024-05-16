<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516145940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
{
    // Ajouter la colonne uniquement si elle n'existe pas déjà
    if (!$schema->getTable('ingredient')->hasColumn('user_id')) {
        $this->addSql('ALTER TABLE ingredient ADD user_id INT DEFAULT NULL');
        $this->addSql('UPDATE ingredient SET user_id = (SELECT id FROM user WHERE ...);');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_ingredient_user FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}

public function down(Schema $schema): void
{
    // Supprimer la colonne et la contrainte de clé étrangère
    $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_ingredient_user');
    $this->addSql('ALTER TABLE ingredient DROP user_id');
}

}
