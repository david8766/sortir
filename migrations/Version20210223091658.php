<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223091658 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE participants');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participants (no_participant INT NOT NULL, pseudo VARCHAR(30) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, nom VARCHAR(30) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, prenom VARCHAR(30) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, telephone VARCHAR(15) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, mail VARCHAR(20) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, mot_de_passe VARCHAR(20) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, administrateur TINYINT(1) NOT NULL, actif TINYINT(1) NOT NULL, campus_no_campus INT NOT NULL, UNIQUE INDEX participants_pseudo_uk (pseudo), PRIMARY KEY(no_participant)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
    }
}
