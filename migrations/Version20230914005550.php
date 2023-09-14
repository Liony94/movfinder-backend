<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230914005550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE friends_request (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, receiver_id INT DEFAULT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_BCFC791FF624B39D (sender_id), INDEX IDX_BCFC791FCD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, movie_db_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, poster_path VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, trailer_url VARCHAR(255) DEFAULT NULL, like_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_user (movie_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7EF5F7448F93B6FC (movie_id), INDEX IDX_7EF5F744A76ED395 (user_id), PRIMARY KEY(movie_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_user_dislike (movie_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_85C013938F93B6FC (movie_id), INDEX IDX_85C01393A76ED395 (user_id), PRIMARY KEY(movie_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie (id INT AUTO_INCREMENT NOT NULL, serie_db_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, poster_path VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, trailer_url VARCHAR(255) DEFAULT NULL, liked_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_user (serie_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_56F6C27BD94388BD (serie_id), INDEX IDX_56F6C27BA76ED395 (user_id), PRIMARY KEY(serie_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_user_dislike (serie_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5453CEDCD94388BD (serie_id), INDEX IDX_5453CEDCA76ED395 (user_id), PRIMARY KEY(serie_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, profile_image VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE friends_request ADD CONSTRAINT FK_BCFC791FF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE friends_request ADD CONSTRAINT FK_BCFC791FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE movie_user ADD CONSTRAINT FK_7EF5F7448F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_user ADD CONSTRAINT FK_7EF5F744A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_user_dislike ADD CONSTRAINT FK_85C013938F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_user_dislike ADD CONSTRAINT FK_85C01393A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_user ADD CONSTRAINT FK_56F6C27BD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_user ADD CONSTRAINT FK_56F6C27BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_user_dislike ADD CONSTRAINT FK_5453CEDCD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_user_dislike ADD CONSTRAINT FK_5453CEDCA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friends_request DROP FOREIGN KEY FK_BCFC791FF624B39D');
        $this->addSql('ALTER TABLE friends_request DROP FOREIGN KEY FK_BCFC791FCD53EDB6');
        $this->addSql('ALTER TABLE movie_user DROP FOREIGN KEY FK_7EF5F7448F93B6FC');
        $this->addSql('ALTER TABLE movie_user DROP FOREIGN KEY FK_7EF5F744A76ED395');
        $this->addSql('ALTER TABLE movie_user_dislike DROP FOREIGN KEY FK_85C013938F93B6FC');
        $this->addSql('ALTER TABLE movie_user_dislike DROP FOREIGN KEY FK_85C01393A76ED395');
        $this->addSql('ALTER TABLE serie_user DROP FOREIGN KEY FK_56F6C27BD94388BD');
        $this->addSql('ALTER TABLE serie_user DROP FOREIGN KEY FK_56F6C27BA76ED395');
        $this->addSql('ALTER TABLE serie_user_dislike DROP FOREIGN KEY FK_5453CEDCD94388BD');
        $this->addSql('ALTER TABLE serie_user_dislike DROP FOREIGN KEY FK_5453CEDCA76ED395');
        $this->addSql('DROP TABLE friends_request');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE movie_user');
        $this->addSql('DROP TABLE movie_user_dislike');
        $this->addSql('DROP TABLE serie');
        $this->addSql('DROP TABLE serie_user');
        $this->addSql('DROP TABLE serie_user_dislike');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
