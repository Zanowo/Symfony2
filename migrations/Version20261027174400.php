<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261027174400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create bookstore entities: customer, book, orders, order_item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL PRIMARY KEY, title VARCHAR(255) NOT NULL, price DECIMAL(10, 2) NOT NULL)');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL PRIMARY KEY, customer_id INT NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D2B3BC398E237E06 ON orders (customer_id)');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL PRIMARY KEY, order_id INT NOT NULL, book_id INT NOT NULL, quantity INT NOT NULL)');
        $this->addSql('CREATE INDEX IDX_8A6C4C3B4AE5C1C8 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_8A6C4C3BAA1A4C3B ON order_item (book_id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_D2B3BC398E237E06 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_8A6C4C3B4AE5C1C8 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_8A6C4C3BAA1A4C3B FOREIGN KEY (book_id) REFERENCES book (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_8A6C4C3BAA1A4C3B');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_8A6C4C3B4AE5C1C8');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_D2B3BC398E237E06');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE order_item');
    }
}
