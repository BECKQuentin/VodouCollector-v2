<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 * Premiere grosse migrations Objects + Basic class des Metadata
 */
final class Version20221210132317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exposition_location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, objects_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, src VARCHAR(255) NOT NULL, INDEX IDX_8C9F36104BEE6933 (objects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE floor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gods (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, objects_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, src VARCHAR(255) NOT NULL, INDEX IDX_C53D045F4BEE6933 (objects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE materials (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE museum_catalogue (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objects_gods (objects_id INT NOT NULL, gods_id INT NOT NULL, INDEX IDX_A5CBF32A4BEE6933 (objects_id), INDEX IDX_A5CBF32AA6E7D247 (gods_id), PRIMARY KEY(objects_id, gods_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objects_materials (objects_id INT NOT NULL, materials_id INT NOT NULL, INDEX IDX_299203044BEE6933 (objects_id), INDEX IDX_299203043A9FC940 (materials_id), PRIMARY KEY(objects_id, materials_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objects_museum_catalogue (objects_id INT NOT NULL, museum_catalogue_id INT NOT NULL, INDEX IDX_A7C40CF54BEE6933 (objects_id), INDEX IDX_A7C40CF5CEA00F07 (museum_catalogue_id), PRIMARY KEY(objects_id, museum_catalogue_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objects_book (objects_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_628D42854BEE6933 (objects_id), INDEX IDX_628D428516A2B381 (book_id), PRIMARY KEY(objects_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE origin (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE population (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, objects_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, src VARCHAR(255) NOT NULL, INDEX IDX_7CC7DA2C4BEE6933 (objects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE youtube (id INT AUTO_INCREMENT NOT NULL, objects_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, src VARCHAR(255) NOT NULL, INDEX IDX_F07899344BEE6933 (objects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36104BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F4BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id)');
        $this->addSql('ALTER TABLE objects_gods ADD CONSTRAINT FK_A5CBF32A4BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objects_gods ADD CONSTRAINT FK_A5CBF32AA6E7D247 FOREIGN KEY (gods_id) REFERENCES gods (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objects_materials ADD CONSTRAINT FK_299203044BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objects_materials ADD CONSTRAINT FK_299203043A9FC940 FOREIGN KEY (materials_id) REFERENCES materials (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objects_museum_catalogue ADD CONSTRAINT FK_A7C40CF54BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objects_museum_catalogue ADD CONSTRAINT FK_A7C40CF5CEA00F07 FOREIGN KEY (museum_catalogue_id) REFERENCES museum_catalogue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objects_book ADD CONSTRAINT FK_628D42854BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objects_book ADD CONSTRAINT FK_628D428516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C4BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id)');
        $this->addSql('ALTER TABLE youtube ADD CONSTRAINT FK_F07899344BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id)');
        $this->addSql('ALTER TABLE objects ADD gods_id INT DEFAULT NULL, ADD created_by_id INT NOT NULL, ADD origin_id INT DEFAULT NULL, ADD population_id INT DEFAULT NULL, ADD exposition_location_id INT NOT NULL, ADD floor_id INT NOT NULL, ADD state_id INT DEFAULT NULL, ADD title VARCHAR(255) NOT NULL, ADD memo LONGTEXT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD era INT DEFAULT NULL, ADD historic_detail LONGTEXT DEFAULT NULL, ADD usage_fonction LONGTEXT DEFAULT NULL, ADD usage_tags LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD usage_user VARCHAR(255) DEFAULT NULL, ADD size_high DOUBLE PRECISION DEFAULT NULL, ADD size_length DOUBLE PRECISION DEFAULT NULL, ADD size_depth DOUBLE PRECISION DEFAULT NULL, ADD weight DOUBLE PRECISION DEFAULT NULL, ADD appearance_tags LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD natural_language_description VARCHAR(255) DEFAULT NULL, ADD inscriptions_engraving VARCHAR(255) DEFAULT NULL, ADD is_basemented TINYINT(1) DEFAULT NULL, ADD showcase_code VARCHAR(255) DEFAULT NULL, ADD shelf VARCHAR(255) DEFAULT NULL, ADD insurance_value VARCHAR(255) DEFAULT NULL, ADD state_commentary VARCHAR(255) DEFAULT NULL, ADD arrived_collection DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE objects ADD CONSTRAINT FK_B21ACCF3A6E7D247 FOREIGN KEY (gods_id) REFERENCES gods (id)');
        $this->addSql('ALTER TABLE objects ADD CONSTRAINT FK_B21ACCF3B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE objects ADD CONSTRAINT FK_B21ACCF356A273CC FOREIGN KEY (origin_id) REFERENCES origin (id)');
        $this->addSql('ALTER TABLE objects ADD CONSTRAINT FK_B21ACCF3C955D1E1 FOREIGN KEY (population_id) REFERENCES population (id)');
        $this->addSql('ALTER TABLE objects ADD CONSTRAINT FK_B21ACCF348405C98 FOREIGN KEY (exposition_location_id) REFERENCES exposition_location (id)');
        $this->addSql('ALTER TABLE objects ADD CONSTRAINT FK_B21ACCF3854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id)');
        $this->addSql('ALTER TABLE objects ADD CONSTRAINT FK_B21ACCF35D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('CREATE INDEX IDX_B21ACCF3A6E7D247 ON objects (gods_id)');
        $this->addSql('CREATE INDEX IDX_B21ACCF3B03A8386 ON objects (created_by_id)');
        $this->addSql('CREATE INDEX IDX_B21ACCF356A273CC ON objects (origin_id)');
        $this->addSql('CREATE INDEX IDX_B21ACCF3C955D1E1 ON objects (population_id)');
        $this->addSql('CREATE INDEX IDX_B21ACCF348405C98 ON objects (exposition_location_id)');
        $this->addSql('CREATE INDEX IDX_B21ACCF3854679E2 ON objects (floor_id)');
        $this->addSql('CREATE INDEX IDX_B21ACCF35D83CC1 ON objects (state_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE objects DROP FOREIGN KEY FK_B21ACCF348405C98');
        $this->addSql('ALTER TABLE objects DROP FOREIGN KEY FK_B21ACCF3854679E2');
        $this->addSql('ALTER TABLE objects DROP FOREIGN KEY FK_B21ACCF3A6E7D247');
        $this->addSql('ALTER TABLE objects DROP FOREIGN KEY FK_B21ACCF356A273CC');
        $this->addSql('ALTER TABLE objects DROP FOREIGN KEY FK_B21ACCF3C955D1E1');
        $this->addSql('ALTER TABLE objects DROP FOREIGN KEY FK_B21ACCF35D83CC1');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36104BEE6933');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F4BEE6933');
        $this->addSql('ALTER TABLE objects_gods DROP FOREIGN KEY FK_A5CBF32A4BEE6933');
        $this->addSql('ALTER TABLE objects_gods DROP FOREIGN KEY FK_A5CBF32AA6E7D247');
        $this->addSql('ALTER TABLE objects_materials DROP FOREIGN KEY FK_299203044BEE6933');
        $this->addSql('ALTER TABLE objects_materials DROP FOREIGN KEY FK_299203043A9FC940');
        $this->addSql('ALTER TABLE objects_museum_catalogue DROP FOREIGN KEY FK_A7C40CF54BEE6933');
        $this->addSql('ALTER TABLE objects_museum_catalogue DROP FOREIGN KEY FK_A7C40CF5CEA00F07');
        $this->addSql('ALTER TABLE objects_book DROP FOREIGN KEY FK_628D42854BEE6933');
        $this->addSql('ALTER TABLE objects_book DROP FOREIGN KEY FK_628D428516A2B381');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C4BEE6933');
        $this->addSql('ALTER TABLE youtube DROP FOREIGN KEY FK_F07899344BEE6933');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE exposition_location');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE floor');
        $this->addSql('DROP TABLE gods');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE materials');
        $this->addSql('DROP TABLE museum_catalogue');
        $this->addSql('DROP TABLE objects_gods');
        $this->addSql('DROP TABLE objects_materials');
        $this->addSql('DROP TABLE objects_museum_catalogue');
        $this->addSql('DROP TABLE objects_book');
        $this->addSql('DROP TABLE origin');
        $this->addSql('DROP TABLE population');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE video');
        $this->addSql('DROP TABLE youtube');
        $this->addSql('ALTER TABLE objects DROP FOREIGN KEY FK_B21ACCF3B03A8386');
        $this->addSql('DROP INDEX IDX_B21ACCF3A6E7D247 ON objects');
        $this->addSql('DROP INDEX IDX_B21ACCF3B03A8386 ON objects');
        $this->addSql('DROP INDEX IDX_B21ACCF356A273CC ON objects');
        $this->addSql('DROP INDEX IDX_B21ACCF3C955D1E1 ON objects');
        $this->addSql('DROP INDEX IDX_B21ACCF348405C98 ON objects');
        $this->addSql('DROP INDEX IDX_B21ACCF3854679E2 ON objects');
        $this->addSql('DROP INDEX IDX_B21ACCF35D83CC1 ON objects');
        $this->addSql('ALTER TABLE objects DROP gods_id, DROP created_by_id, DROP origin_id, DROP population_id, DROP exposition_location_id, DROP floor_id, DROP state_id, DROP title, DROP memo, DROP created_at, DROP era, DROP historic_detail, DROP usage_fonction, DROP usage_tags, DROP usage_user, DROP size_high, DROP size_length, DROP size_depth, DROP weight, DROP appearance_tags, DROP natural_language_description, DROP inscriptions_engraving, DROP is_basemented, DROP showcase_code, DROP shelf, DROP insurance_value, DROP state_commentary, DROP arrived_collection');
    }
}
