USE josoftware_db;

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
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    company_id   INT UNSIGNED  NOT NULL,
    name         VARCHAR(150)  NOT NULL,
    function     VARCHAR(100)      NULL DEFAULT NULL,
    email        VARCHAR(150)      NULL DEFAULT NULL,
    phone        VARCHAR(30)       NULL DEFAULT NULL,
    notes        TEXT              NULL DEFAULT NULL,
    created_at   DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_contacts_company (company_id),
    CONSTRAINT fk_contacts_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_log (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    company_id   INT UNSIGNED  NOT NULL,
    contact_id   INT UNSIGNED      NULL DEFAULT NULL,
    user_id      INT UNSIGNED      NULL DEFAULT NULL,
    type         ENUM('telefoongesprek','email','meeting','overig') NOT NULL DEFAULT 'telefoongesprek',
    notes        TEXT          NOT NULL,
    logged_at    DATETIME      NOT NULL,
    created_at   DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY idx_log_company (company_id),
    CONSTRAINT fk_log_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
    CONSTRAINT fk_log_contact FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE SET NULL,
    CONSTRAINT fk_log_user    FOREIGN KEY (user_id)    REFERENCES users    (id) ON DELETE SET NULL
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
