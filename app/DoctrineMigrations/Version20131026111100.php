<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131026111100 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE task_period (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, task_id INT DEFAULT NULL, dateBegin DATETIME NOT NULL, dateEnd DATETIME NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_FE119A1D7E3C61F9 (owner_id), INDEX IDX_FE119A1D8DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE task_period ADD CONSTRAINT FK_FE119A1D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id)");
        $this->addSql("ALTER TABLE task_period ADD CONSTRAINT FK_FE119A1D8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE task_period");
    }
}
