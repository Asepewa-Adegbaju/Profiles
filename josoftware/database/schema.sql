-- ═════════════════════════════════════════════════════════════════════════════
-- JO Software Solutions — Volledig Database Schema
-- Uitvoeren: mysql -u root < schema.sql
-- Of via phpMyAdmin: importeer dit bestand
-- ═════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS josoftware_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE josoftware_db;

SET FOREIGN_KEY_CHECKS = 0;

-- ─── Gebruikers ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL,
    password   VARCHAR(255)  NOT NULL,
    created_at DATETIME      NOT NULL,
    updated_at DATETIME          NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Audit log ───────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS audit_log (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED      NULL DEFAULT NULL,
    action      VARCHAR(50)   NOT NULL,
    table_name  VARCHAR(60)   NOT NULL,
    record_id   INT UNSIGNED      NULL DEFAULT NULL,
    description TEXT              NULL DEFAULT NULL,
    ip_address  VARCHAR(45)       NULL DEFAULT NULL,
    created_at  DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_audit_user    (user_id),
    KEY idx_audit_table   (table_name, record_id),
    KEY idx_audit_created (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── CRM ─────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS companies (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name         VARCHAR(200)  NOT NULL,
    sector       VARCHAR(100)      NULL DEFAULT NULL,
    phone        VARCHAR(30)       NULL DEFAULT NULL,
    email        VARCHAR(150)      NULL DEFAULT NULL,
    website      VARCHAR(200)      NULL DEFAULT NULL,
    address      VARCHAR(300)      NULL DEFAULT NULL,
    city         VARCHAR(100)      NULL DEFAULT NULL,
    postal_code  VARCHAR(20)       NULL DEFAULT NULL,
    status       ENUM('lead','prospect','klant','inactief') NOT NULL DEFAULT 'lead',
    notes        TEXT              NULL DEFAULT NULL,
    created_by   INT UNSIGNED      NULL DEFAULT NULL,
    created_at   DATETIME      NOT NULL,
    updated_at   DATETIME          NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_companies_status (status),
    CONSTRAINT fk_companies_user FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contacts (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    company_id  INT UNSIGNED  NOT NULL,
    name        VARCHAR(150)  NOT NULL,
    function    VARCHAR(100)      NULL DEFAULT NULL,
    email       VARCHAR(150)      NULL DEFAULT NULL,
    phone       VARCHAR(30)       NULL DEFAULT NULL,
    notes       TEXT              NULL DEFAULT NULL,
    created_at  DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_contacts_company (company_id),
    CONSTRAINT fk_contacts_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_log (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    company_id  INT UNSIGNED  NOT NULL,
    contact_id  INT UNSIGNED      NULL DEFAULT NULL,
    user_id     INT UNSIGNED      NULL DEFAULT NULL,
    type        ENUM('telefoongesprek','email','meeting','overig') NOT NULL DEFAULT 'telefoongesprek',
    notes       TEXT          NOT NULL,
    logged_at   DATETIME      NOT NULL,
    created_at  DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_log_company (company_id),
    CONSTRAINT fk_log_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
    CONSTRAINT fk_log_contact FOREIGN KEY (contact_id) REFERENCES contacts  (id) ON DELETE SET NULL,
    CONSTRAINT fk_log_user    FOREIGN KEY (user_id)    REFERENCES users      (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS meetings (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    company_id   INT UNSIGNED  NOT NULL,
    contact_id   INT UNSIGNED      NULL DEFAULT NULL,
    user_id      INT UNSIGNED      NULL DEFAULT NULL,
    title        VARCHAR(200)  NOT NULL,
    meeting_date DATETIME      NOT NULL,
    location     VARCHAR(200)      NULL DEFAULT NULL,
    status       ENUM('gepland','bevestigd','geweest','afgeslagen') NOT NULL DEFAULT 'gepland',
    notes        TEXT              NULL DEFAULT NULL,
    created_at   DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_meetings_date    (meeting_date),
    KEY idx_meetings_company (company_id),
    CONSTRAINT fk_meetings_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
    CONSTRAINT fk_meetings_contact FOREIGN KEY (contact_id) REFERENCES contacts  (id) ON DELETE SET NULL,
    CONSTRAINT fk_meetings_user    FOREIGN KEY (user_id)    REFERENCES users      (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Projecten & Taken ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS projects (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    company_id   INT UNSIGNED      NULL DEFAULT NULL,
    name         VARCHAR(200)  NOT NULL,
    description  TEXT              NULL DEFAULT NULL,
    status       ENUM('actief','afgerond','on-hold','geannuleerd') NOT NULL DEFAULT 'actief',
    start_date   DATE              NULL DEFAULT NULL,
    end_date     DATE              NULL DEFAULT NULL,
    budget       DECIMAL(10,2)     NULL DEFAULT NULL,
    created_by   INT UNSIGNED      NULL DEFAULT NULL,
    created_at   DATETIME      NOT NULL,
    updated_at   DATETIME          NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_projects_status  (status),
    KEY idx_projects_company (company_id),
    CONSTRAINT fk_projects_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL,
    CONSTRAINT fk_projects_user    FOREIGN KEY (created_by) REFERENCES users     (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tasks (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    project_id   INT UNSIGNED  NOT NULL,
    assigned_to  INT UNSIGNED      NULL DEFAULT NULL,
    title        VARCHAR(300)  NOT NULL,
    description  TEXT              NULL DEFAULT NULL,
    status       ENUM('te-doen','bezig','review','klaar') NOT NULL DEFAULT 'te-doen',
    priority     ENUM('laag','normaal','hoog','urgent')   NOT NULL DEFAULT 'normaal',
    due_date     DATE              NULL DEFAULT NULL,
    completed_at DATETIME          NULL DEFAULT NULL,
    created_at   DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_tasks_project (project_id),
    KEY idx_tasks_status  (status),
    CONSTRAINT fk_tasks_project  FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_assigned FOREIGN KEY (assigned_to) REFERENCES users   (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Uren & Kilometers ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS time_entries (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id      INT UNSIGNED  NOT NULL,
    project_id   INT UNSIGNED      NULL DEFAULT NULL,
    company_id   INT UNSIGNED      NULL DEFAULT NULL,
    entry_date   DATE          NOT NULL,
    description  VARCHAR(500)  NOT NULL,
    hours        DECIMAL(5,2)  NOT NULL,
    hourly_rate  DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
    type         ENUM('zakelijk','prive') NOT NULL DEFAULT 'zakelijk',
    billable     TINYINT(1)    NOT NULL DEFAULT 1,
    created_at   DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_time_user    (user_id),
    KEY idx_time_date    (entry_date),
    KEY idx_time_project (project_id),
    CONSTRAINT fk_time_user    FOREIGN KEY (user_id)    REFERENCES users     (id) ON DELETE CASCADE,
    CONSTRAINT fk_time_project FOREIGN KEY (project_id) REFERENCES projects  (id) ON DELETE SET NULL,
    CONSTRAINT fk_time_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS km_entries (
    id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id        INT UNSIGNED  NOT NULL,
    entry_date     DATE          NOT NULL,
    from_location  VARCHAR(200)  NOT NULL,
    to_location    VARCHAR(200)  NOT NULL,
    km             DECIMAL(7,2)  NOT NULL,
    purpose        VARCHAR(300)  NOT NULL,
    type           ENUM('zakelijk','prive') NOT NULL DEFAULT 'zakelijk',
    rate_per_km    DECIMAL(5,3)  NOT NULL DEFAULT 0.230,
    created_at     DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_km_user (user_id),
    KEY idx_km_date (entry_date),
    CONSTRAINT fk_km_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Financiën ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS quotes (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    quote_number  VARCHAR(30)   NOT NULL,
    company_id    INT UNSIGNED  NOT NULL,
    created_by    INT UNSIGNED      NULL DEFAULT NULL,
    issue_date    DATE          NOT NULL,
    valid_until   DATE          NOT NULL,
    status        ENUM('concept','verzonden','geaccepteerd','afgewezen','verlopen') NOT NULL DEFAULT 'concept',
    notes         TEXT              NULL DEFAULT NULL,
    created_at    DATETIME      NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_quote_number (quote_number),
    KEY idx_quotes_company (company_id),
    CONSTRAINT fk_quotes_company FOREIGN KEY (company_id) REFERENCES companies (id),
    CONSTRAINT fk_quotes_user    FOREIGN KEY (created_by) REFERENCES users     (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quote_items (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    quote_id     INT UNSIGNED  NOT NULL,
    description  VARCHAR(500)  NOT NULL,
    quantity     DECIMAL(8,2)  NOT NULL DEFAULT 1.00,
    unit_price   DECIMAL(10,2) NOT NULL,
    vat_rate     DECIMAL(5,2)  NOT NULL DEFAULT 21.00,
    sort_order   INT           NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_qi_quote (quote_id),
    CONSTRAINT fk_quote_items FOREIGN KEY (quote_id) REFERENCES quotes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoices (
    id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    invoice_number  VARCHAR(30)   NOT NULL,
    company_id      INT UNSIGNED  NOT NULL,
    quote_id        INT UNSIGNED      NULL DEFAULT NULL,
    created_by      INT UNSIGNED      NULL DEFAULT NULL,
    issue_date      DATE          NOT NULL,
    due_date        DATE          NOT NULL,
    status          ENUM('concept','verzonden','betaald','te-laat','geannuleerd') NOT NULL DEFAULT 'concept',
    notes           TEXT              NULL DEFAULT NULL,
    created_at      DATETIME      NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_invoice_number (invoice_number),
    KEY idx_invoices_company (company_id),
    CONSTRAINT fk_invoices_company FOREIGN KEY (company_id) REFERENCES companies (id),
    CONSTRAINT fk_invoices_quote   FOREIGN KEY (quote_id)   REFERENCES quotes    (id) ON DELETE SET NULL,
    CONSTRAINT fk_invoices_user    FOREIGN KEY (created_by) REFERENCES users     (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoice_items (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    invoice_id   INT UNSIGNED  NOT NULL,
    description  VARCHAR(500)  NOT NULL,
    quantity     DECIMAL(8,2)  NOT NULL DEFAULT 1.00,
    unit_price   DECIMAL(10,2) NOT NULL,
    vat_rate     DECIMAL(5,2)  NOT NULL DEFAULT 21.00,
    sort_order   INT           NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_ii_invoice (invoice_id),
    CONSTRAINT fk_invoice_items FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
