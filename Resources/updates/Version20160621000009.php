<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160621000009 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE campaignchain_operation_slideshare_slideshow (id INT AUTO_INCREMENT NOT NULL, operation_id INT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, identifier INT DEFAULT NULL, createdDate DATETIME NOT NULL, modifiedDate DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_EA83505344AC3583 (operation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campaignchain_operation_slideshare_slideshow ADD CONSTRAINT FK_EA83505344AC3583 FOREIGN KEY (operation_id) REFERENCES campaignchain_operation (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE campaignchain_operation_slideshare_slideshow');
    }
}