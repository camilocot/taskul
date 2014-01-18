<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140115220654 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE lexik_email_translation DROP FOREIGN KEY FK_6ED9BAA2A832C1C9");
        $this->addSql("ALTER TABLE lexik_email DROP FOREIGN KEY FK_D781892E8C22AA1A");
        $this->addSql("ALTER TABLE lexik_layout_translation DROP FOREIGN KEY FK_495DCB868C22AA1A");
        $this->addSql("DROP TABLE fos_group_audit");
        $this->addSql("DROP TABLE fos_user_audit");
        $this->addSql("DROP TABLE fos_user_user_group");
        $this->addSql("DROP TABLE lexik_email");
        $this->addSql("DROP TABLE lexik_email_translation");
        $this->addSql("DROP TABLE lexik_layout");
        $this->addSql("DROP TABLE lexik_layout_translation");
        $this->addSql("DROP TABLE revisions");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE fos_group_audit (id INT NOT NULL, rev INT NOT NULL, name VARCHAR(255) DEFAULT NULL, roles LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE fos_user_audit (id INT NOT NULL, rev INT NOT NULL, username VARCHAR(255) DEFAULT NULL, username_canonical VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, email_canonical VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) DEFAULT NULL, expired TINYINT(1) DEFAULT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', credentials_expired TINYINT(1) DEFAULT NULL, credentials_expire_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, date_of_birth DATETIME DEFAULT NULL, firstname VARCHAR(64) DEFAULT NULL, lastname VARCHAR(64) DEFAULT NULL, website VARCHAR(64) DEFAULT NULL, biography VARCHAR(255) DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, locale VARCHAR(8) DEFAULT NULL, timezone VARCHAR(64) DEFAULT NULL, phone VARCHAR(64) DEFAULT NULL, facebook_uid VARCHAR(255) DEFAULT NULL, facebook_name VARCHAR(255) DEFAULT NULL, facebook_data LONGTEXT DEFAULT NULL, twitter_uid VARCHAR(255) DEFAULT NULL, twitter_name VARCHAR(255) DEFAULT NULL, twitter_data LONGTEXT DEFAULT NULL, gplus_uid VARCHAR(255) DEFAULT NULL, gplus_name VARCHAR(255) DEFAULT NULL, gplus_data LONGTEXT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, two_step_code VARCHAR(255) DEFAULT NULL, facebookId VARCHAR(255) DEFAULT NULL, code_upload VARCHAR(255) DEFAULT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE lexik_email (id INT AUTO_INCREMENT NOT NULL, layout_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, bcc VARCHAR(255) DEFAULT NULL, spool TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_D781892EAEA34913 (reference), INDEX IDX_D781892E8C22AA1A (layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE lexik_email_translation (id INT AUTO_INCREMENT NOT NULL, email_id INT DEFAULT NULL, lang VARCHAR(2) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, body_text LONGTEXT DEFAULT NULL, from_address VARCHAR(255) DEFAULT NULL, from_name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6ED9BAA2A832C1C9 (email_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE lexik_layout (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B1B4C0FDAEA34913 (reference), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE lexik_layout_translation (id INT AUTO_INCREMENT NOT NULL, layout_id INT DEFAULT NULL, lang VARCHAR(2) NOT NULL, body LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_495DCB868C22AA1A (layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE revisions (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, username VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE lexik_email ADD CONSTRAINT FK_D781892E8C22AA1A FOREIGN KEY (layout_id) REFERENCES lexik_layout (id)");
        $this->addSql("ALTER TABLE lexik_email_translation ADD CONSTRAINT FK_6ED9BAA2A832C1C9 FOREIGN KEY (email_id) REFERENCES lexik_email (id)");
        $this->addSql("ALTER TABLE lexik_layout_translation ADD CONSTRAINT FK_495DCB868C22AA1A FOREIGN KEY (layout_id) REFERENCES lexik_layout (id)");
    }
}
