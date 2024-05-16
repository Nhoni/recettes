<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516150922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->getTable('ingredient')->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE ingredient ADD user_id INT DEFAULT NULL');
            $this->addSql('UPDATE ingredient SET user_id = (SELECT id FROM user WHERE ...);');
            $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_ingredient_user FOREIGN KEY (user_id) REFERENCES user (id)');
        }
    
        if (!$schema->getTable('recipe')->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE recipe ADD user_id INT DEFAULT NULL');
            $this->addSql('UPDATE recipe SET user_id = (SELECT id FROM user WHERE ...);');
            $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_recipe_user FOREIGN KEY (user_id) REFERENCES user (id)');
        }
    }
    
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_ingredient_user');
        $this->addSql('ALTER TABLE ingredient DROP user_id');
    
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_recipe_user');
        $this->addSql('ALTER TABLE recipe DROP user_id');
    }
    

}
