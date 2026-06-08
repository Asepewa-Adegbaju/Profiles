USE josoftware_db;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS quotes (
    id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    quote_number  VARCHAR(30)     NOT NULL,
    company_id    INT UNSIGNED    NOT NULL,
    created_by    INT UNSIGNED        NULL DEFAULT NULL,
    issue_date    DATE            NOT NULL,
    valid_until   DATE            NOT NULL,
    status        ENUM('concept','verzonden','geaccepteerd','afgewezen','verlopen') NOT NULL DEFAULT 'concept',
    notes         TEXT                NULL DEFAULT NULL,
    created_at    DATETIME        NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_quote_number (quote_number),
    KEY idx_quotes_company (company_id),
    CONSTRAINT fk_quotes_company FOREIGN KEY (company_id) REFERENCES companies (id),
    CONSTRAINT fk_quotes_user    FOREIGN KEY (created_by) REFERENCES users     (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quote_items (
    id           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    quote_id     INT UNSIGNED    NOT NULL,
    description  VARCHAR(500)    NOT NULL,
    quantity     DECIMAL(8,2)    NOT NULL DEFAULT 1.00,
    unit_price   DECIMAL(10,2)   NOT NULL,
    vat_rate     DECIMAL(5,2)    NOT NULL DEFAULT 21.00,
    sort_order   INT             NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_qi_quote (quote_id),
    CONSTRAINT fk_quote_items FOREIGN KEY (quote_id) REFERENCES quotes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoices (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    invoice_number  VARCHAR(30)     NOT NULL,
    company_id      INT UNSIGNED    NOT NULL,
    quote_id        INT UNSIGNED        NULL DEFAULT NULL,
    created_by      INT UNSIGNED        NULL DEFAULT NULL,
    issue_date      DATE            NOT NULL,
    due_date        DATE            NOT NULL,
    status          ENUM('concept','verzonden','betaald','te-laat','geannuleerd') NOT NULL DEFAULT 'concept',
    notes           TEXT                NULL DEFAULT NULL,
    created_at      DATETIME        NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_invoice_number (invoice_number),
    KEY idx_invoices_company (company_id),
    CONSTRAINT fk_invoices_company FOREIGN KEY (company_id) REFERENCES companies (id),
    CONSTRAINT fk_invoices_quote   FOREIGN KEY (quote_id)   REFERENCES quotes    (id) ON DELETE SET NULL,
    CONSTRAINT fk_invoices_user    FOREIGN KEY (created_by) REFERENCES users     (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoice_items (
    id           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    invoice_id   INT UNSIGNED    NOT NULL,
    description  VARCHAR(500)    NOT NULL,
    quantity     DECIMAL(8,2)    NOT NULL DEFAULT 1.00,
    unit_price   DECIMAL(10,2)   NOT NULL,
    vat_rate     DECIMAL(5,2)    NOT NULL DEFAULT 21.00,
    sort_order   INT             NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_ii_invoice (invoice_id),
    CONSTRAINT fk_invoice_items FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
