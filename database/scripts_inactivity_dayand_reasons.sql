-- Tablas finales definidas por las migraciones 2026_10_17.
-- Base de datos destino: if0_42899012_pangea
-- Requiere que la tabla employees ya exista.
USE `if0_42899012_pangea`;

CREATE TABLE `inactivity_reasons` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `inactivity_reasons_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inactivity_record` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `inactivity_reason_id` BIGINT UNSIGNED NOT NULL,
    `is_paid` TINYINT(1) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `inactivity_record_employee_id_is_active_index` (`employee_id`, `is_active`),
    CONSTRAINT `inactivity_record_employee_id_foreign`
        FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `inactivity_record_inactivity_reason_id_foreign`
        FOREIGN KEY (`inactivity_reason_id`) REFERENCES `inactivity_reasons` (`id`)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inactivity_record_days` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `inactivity_record_id` BIGINT UNSIGNED NOT NULL,
    `date` DATE NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `inactivity_record_days_inactivity_record_id_date_unique`
        (`inactivity_record_id`, `date`),
    CONSTRAINT `inactivity_record_days_inactivity_record_id_foreign`
        FOREIGN KEY (`inactivity_record_id`) REFERENCES `inactivity_record` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;