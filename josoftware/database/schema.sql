-- ─────────────────────────────────────────────────────────────────────────────
-- JO Software Solutions — Database Schema
-- Uitvoeren via: mysql -u root josoftware_db < schema.sql
-- ─────────────────────────────────────────────────────────────────────────────

CREATE DATABASE IF NOT EXISTS josoftware_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE josoftware_db;

-- ─── Gebruikers ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100)     NOT NULL,
    email      VARCHAR(150)     NOT NULL,
    password   VARCHAR(255)     NOT NULL,
    created_at DATETIME         NOT NULL,
    updated_at DATETIME             NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Audit log ───────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS audit_log (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED         NULL DEFAULT NULL,
    action      VARCHAR(50)      NOT NULL,         -- bijv. 'create', 'update', 'delete', 'login'
    table_name  VARCHAR(60)      NOT NULL,
    record_id   INT UNSIGNED         NULL DEFAULT NULL,
    description TEXT                 NULL DEFAULT NULL,
    ip_address  VARCHAR(45)          NULL DEFAULT NULL,
    created_at  DATETIME         NOT NULL,
    PRIMARY KEY (id),
    KEY idx_audit_user     (user_id),
    KEY idx_audit_table    (table_name, record_id),
    KEY idx_audit_created  (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- MODULES — worden per stap uitgebreid
-- CRM, Uren, Projecten, Financiën tabellen volgen in volgende stappen
-- ─────────────────────────────────────────────────────────────────────────────
