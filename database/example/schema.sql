-- Base de datos de prueba para la aplicación Issueflow

-- Elimina la base de datos si existe
DROP DATABASE IF EXISTS issueflow_sql_lab;

-- Crear la base de datos
CREATE DATABASE issueflow_sql_lab
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

-- Digo que voy a usar la base de datos que acabo de crear
USE issueflow_sql_lab;

-- Creo la tabla users en la base de datos
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT uq_users_email UNIQUE (email)
);

-- Creo la tabla categories
CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT uq_categories_name UNIQUE (name)
);

-- Creo la tabla tickets
CREATE TABLE tickets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code CHAR(8) NOT NULL,
    title VARCHAR(180) NOT NULL,
    priority VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'open',
    user_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT uq_tickets_code UNIQUE (code),
    CONSTRAINT chk_tickets_priority CHECK (priority IN ('urgent', 'high', 'normal', 'low')),
    CONSTRAINT chk_tickets_status CHECK (status IN ('open', 'in_progress', 'closed')),
    CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_tickets_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
);