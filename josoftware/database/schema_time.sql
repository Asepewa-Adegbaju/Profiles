SET FOREIGN_KEY_CHECKS = 0;

USE josoftware_db;

CREATE TABLE IF NOT EXISTS time_entries (
    id           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    user_id      INT UNSIGNED   NOT NULL,
    project_id   INT UNSIGNED       NULL DEFAULT NULL,
    company_id   INT UNSIGNED       NULL DEFAULT NULL,
    entry_date   DATE           NOT NULL,
    description  VARCHAR(500)   NOT NULL,
    hours        DECIMAL(5,2)   NOT NULL,
    hourly_rate  DECIMAL(8,2)   NOT NULL DEFAULT 0.00,
    type         ENUM('zakelijk','prive') NOT NULL DEFAULT 'zakelijk',
    billable     TINYINT(1)     NOT NULL DEFAULT 1,
    created_at   DATETIME       NOT NULL,
    PRIMARY KEY (id),
    KEY idx_time_user    (user_id),
    KEY idx_time_date    (entry_date),
    KEY idx_time_project (project_id),
    CONSTRAINT fk_time_user    FOREIGN KEY (user_id)    REFERENCES users    (id) ON DELETE CASCADE,
    CONSTRAINT fk_time_project FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE SET NULL,
    CONSTRAINT fk_time_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS km_entries (
    id             INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    user_id        INT UNSIGNED   NOT NULL,
    entry_date     DATE           NOT NULL,
    from_location  VARCHAR(200)   NOT NULL,
    to_location    VARCHAR(200)   NOT NULL,
    km             DECIMAL(7,2)   NOT NULL,
    purpose        VARCHAR(300)   NOT NULL,
    type           ENUM('zakelijk','prive') NOT NULL DEFAULT 'zakelijk',
    rate_per_km    DECIMAL(5,3)   NOT NULL DEFAULT 0.230,
    created_at     DATETIME       NOT NULL,
    PRIMARY KEY (id),
    KEY idx_km_user (user_id),
    KEY idx_km_date (entry_date),
    CONSTRAINT fk_km_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
