<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530133152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE contains_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE custom_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE doctor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE hospital_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE medicine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE provides_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE contains (id INT NOT NULL, custom_id INT NOT NULL, medicine_id INT NOT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8EFA6A7E614A603A ON contains (custom_id)');
        $this->addSql('CREATE INDEX IDX_8EFA6A7E2F7D140A ON contains (medicine_id)');
        $this->addSql('CREATE TABLE custom (id INT NOT NULL, customer_id INT NOT NULL, courier_id INT DEFAULT NULL, doctor_id INT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, payment_date DATE NOT NULL, complete_date DATE NOT NULL, is_in_cart BOOLEAN NOT NULL, address VARCHAR(255) NOT NULL, is_ready BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F584169B9395C3F3 ON custom (customer_id)');
        $this->addSql('CREATE INDEX IDX_F584169BE3D8151C ON custom (courier_id)');
        $this->addSql('CREATE INDEX IDX_F584169B87F4FB17 ON custom (doctor_id)');
        $this->addSql('CREATE TABLE doctor (id INT NOT NULL, user_profile_id INT NOT NULL, hospital_id INT NOT NULL, specialization VARCHAR(255) NOT NULL, post VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1FC0F36A6B9DD454 ON doctor (user_profile_id)');
        $this->addSql('CREATE INDEX IDX_1FC0F36A63DBB69 ON doctor (hospital_id)');
        $this->addSql('CREATE TABLE hospital (id INT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(13) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE medicine (id INT NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, compound VARCHAR(255) NOT NULL, pharmgroup VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE provider (id INT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(13) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE provides (id INT NOT NULL, provider_id INT NOT NULL, medicine_id INT NOT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E5C3430AA53A8AA ON provides (provider_id)');
        $this->addSql('CREATE INDEX IDX_E5C3430A2F7D140A ON provides (medicine_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, patronymic VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(13) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE contains ADD CONSTRAINT FK_8EFA6A7E614A603A FOREIGN KEY (custom_id) REFERENCES custom (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contains ADD CONSTRAINT FK_8EFA6A7E2F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE custom ADD CONSTRAINT FK_F584169B9395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE custom ADD CONSTRAINT FK_F584169BE3D8151C FOREIGN KEY (courier_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE custom ADD CONSTRAINT FK_F584169B87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36A6B9DD454 FOREIGN KEY (user_profile_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36A63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE provides ADD CONSTRAINT FK_E5C3430AA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE provides ADD CONSTRAINT FK_E5C3430A2F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contains DROP CONSTRAINT FK_8EFA6A7E614A603A');
        $this->addSql('ALTER TABLE custom DROP CONSTRAINT FK_F584169B87F4FB17');
        $this->addSql('ALTER TABLE doctor DROP CONSTRAINT FK_1FC0F36A63DBB69');
        $this->addSql('ALTER TABLE contains DROP CONSTRAINT FK_8EFA6A7E2F7D140A');
        $this->addSql('ALTER TABLE provides DROP CONSTRAINT FK_E5C3430A2F7D140A');
        $this->addSql('ALTER TABLE provides DROP CONSTRAINT FK_E5C3430AA53A8AA');
        $this->addSql('ALTER TABLE custom DROP CONSTRAINT FK_F584169B9395C3F3');
        $this->addSql('ALTER TABLE custom DROP CONSTRAINT FK_F584169BE3D8151C');
        $this->addSql('ALTER TABLE doctor DROP CONSTRAINT FK_1FC0F36A6B9DD454');
        $this->addSql('DROP SEQUENCE contains_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE custom_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE doctor_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE hospital_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE medicine_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE provider_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE provides_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE contains');
        $this->addSql('DROP TABLE custom');
        $this->addSql('DROP TABLE doctor');
        $this->addSql('DROP TABLE hospital');
        $this->addSql('DROP TABLE medicine');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE provides');
        $this->addSql('DROP TABLE "user"');
    }
}
