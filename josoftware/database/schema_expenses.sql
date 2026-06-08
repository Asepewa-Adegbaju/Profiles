USE josoftware_db;

CREATE TABLE IF NOT EXISTS expense_categories (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100)  NOT NULL,
    color      VARCHAR(7)    NOT NULL DEFAULT '#64748b',
    created_at DATETIME      NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS expenses (
    id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    user_id          INT UNSIGNED   NOT NULL,
    category_id      INT UNSIGNED       NULL DEFAULT NULL,
    company_id       INT UNSIGNED       NULL DEFAULT NULL,
    project_id       INT UNSIGNED       NULL DEFAULT NULL,
    entry_date       DATE           NOT NULL,
    amount           DECIMAL(10,2)  NOT NULL,
    vat_rate         DECIMAL(5,2)   NOT NULL DEFAULT 21.00,
    description      VARCHAR(500)   NOT NULL,
    supplier         VARCHAR(200)       NULL DEFAULT NULL,
    receipt_filename VARCHAR(255)       NULL DEFAULT NULL,
    type             ENUM('zakelijk','prive') NOT NULL DEFAULT 'zakelijk',
    status           ENUM('ingediend','goedgekeurd','afgewezen') NOT NULL DEFAULT 'ingediend',
    notes            TEXT               NULL DEFAULT NULL,
    created_at       DATETIME       NOT NULL,
    PRIMARY KEY (id),
    KEY idx_exp_user     (user_id),
    KEY idx_exp_date     (entry_date),
    KEY idx_exp_category (category_id),
    CONSTRAINT fk_exp_user     FOREIGN KEY (user_id)     REFERENCES users     (id) ON DELETE CASCADE,
    CONSTRAINT fk_exp_category FOREIGN KEY (category_id) REFERENCES expense_categories (id) ON DELETE SET NULL,
    CONSTRAINT fk_exp_company  FOREIGN KEY (company_id)  REFERENCES companies (id) ON DELETE SET NULL,
    CONSTRAINT fk_exp_project  FOREIGN KEY (project_id)  REFERENCES projects  (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standaard categorieën
INSERT IGNORE INTO expense_categories (name, color, created_at) VALUES
('Kantoorbenodigdheden',       '#3b82f6', NOW()),
('Reiskosten',                 '#8b5cf6', NOW()),
('Maaltijden & representatie', '#f59e0b', NOW()),
('Software & abonnementen',    '#06b6d4', NOW()),
('Marketing & reclame',        '#ec4899', NOW()),
('Telecom',                    '#10b981', NOW()),
('Overig',                     '#64748b', NOW());
