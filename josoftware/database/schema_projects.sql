USE josoftware_db;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS projects (
    id           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    company_id   INT UNSIGNED       NULL DEFAULT NULL,
    name         VARCHAR(200)   NOT NULL,
    description  TEXT               NULL DEFAULT NULL,
    status       ENUM('actief','afgerond','on-hold','geannuleerd') NOT NULL DEFAULT 'actief',
    start_date   DATE               NULL DEFAULT NULL,
    end_date     DATE               NULL DEFAULT NULL,
    budget       DECIMAL(10,2)      NULL DEFAULT NULL,
    created_by   INT UNSIGNED       NULL DEFAULT NULL,
    created_at   DATETIME       NOT NULL,
    updated_at   DATETIME           NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_projects_status  (status),
    KEY idx_projects_company (company_id),
    CONSTRAINT fk_projects_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL,
    CONSTRAINT fk_projects_user    FOREIGN KEY (created_by) REFERENCES users     (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tasks (
    id           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    project_id   INT UNSIGNED   NOT NULL,
    assigned_to  INT UNSIGNED       NULL DEFAULT NULL,
    title        VARCHAR(300)   NOT NULL,
    description  TEXT               NULL DEFAULT NULL,
    status       ENUM('te-doen','bezig','review','klaar') NOT NULL DEFAULT 'te-doen',
    priority     ENUM('laag','normaal','hoog','urgent')   NOT NULL DEFAULT 'normaal',
    due_date     DATE               NULL DEFAULT NULL,
    completed_at DATETIME           NULL DEFAULT NULL,
    created_at   DATETIME       NOT NULL,
    PRIMARY KEY (id),
    KEY idx_tasks_project (project_id),
    KEY idx_tasks_status  (status),
    CONSTRAINT fk_tasks_project  FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_assigned FOREIGN KEY (assigned_to) REFERENCES users   (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
