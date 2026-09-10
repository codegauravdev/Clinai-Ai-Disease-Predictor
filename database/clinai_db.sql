-- ============================================================
--  ClinAI Disease Predictor — MySQL Database Schema
--  Import this file via phpMyAdmin:
--    1. Open phpMyAdmin
--    2. Click "New" to create a database named: clinai_db
--    3. Select clinai_db → click "Import" → choose this file
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Create & use the database
CREATE DATABASE IF NOT EXISTS `clinai_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `clinai_db`;

-- ──────────────────────────────────────────────────────────────
-- Table: users  (hospital staff accounts)
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name`    VARCHAR(120) NOT NULL,
  `email`        VARCHAR(180) NOT NULL UNIQUE,
  `password`     VARCHAR(255) NOT NULL,              -- bcrypt hash
  `role`         ENUM('doctor','nurse','admin') NOT NULL DEFAULT 'doctor',
  `department`   VARCHAR(100) DEFAULT NULL,
  `avatar_color` VARCHAR(7)   DEFAULT '#4f6ef7',     -- hex, for UI avatar
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login`   DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo admin account  (password: Admin@123)
INSERT INTO `users` (`full_name`, `email`, `password`, `role`, `department`) VALUES
('Dr. Admin', 'admin@clinai.local',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',   -- Admin@123
 'admin', 'Administration');

-- ──────────────────────────────────────────────────────────────
-- Table: patients
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `patients` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mrn`          VARCHAR(30)  DEFAULT NULL COMMENT 'Medical Record Number',
  `full_name`    VARCHAR(150) NOT NULL,
  `age`          TINYINT UNSIGNED NOT NULL,
  `gender`       ENUM('male','female','other') NOT NULL,
  `weight_kg`    DECIMAL(5,1) DEFAULT NULL,
  `height_cm`    DECIMAL(5,1) DEFAULT NULL,
  `bmi`          DECIMAL(4,1) GENERATED ALWAYS AS (
                   CASE WHEN height_cm > 0
                     THEN ROUND(weight_kg / ((height_cm/100) * (height_cm/100)), 1)
                     ELSE NULL END
                 ) VIRTUAL,
  `ward`         VARCHAR(80)  DEFAULT NULL,
  `created_by`   INT UNSIGNED DEFAULT NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_patients_user` (`created_by`),
  CONSTRAINT `fk_patients_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────────
-- Table: assessments  (one row per AI prediction run)
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `assessments` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `patient_id`        INT UNSIGNED NOT NULL,
  `assessed_by`       INT UNSIGNED DEFAULT NULL,
  -- Risk factors (stored as boolean flags)
  `rf_smoker`         TINYINT(1) DEFAULT 0,
  `rf_diabetes`       TINYINT(1) DEFAULT 0,
  `rf_hypertension`   TINYINT(1) DEFAULT 0,
  `rf_immunocompromised` TINYINT(1) DEFAULT 0,
  `rf_pregnant`       TINYINT(1) DEFAULT 0,
  `rf_family_history` TINYINT(1) DEFAULT 0,
  `rf_travel`         TINYINT(1) DEFAULT 0,
  `rf_tb_contact`     TINYINT(1) DEFAULT 0,
  `rf_hiv`            TINYINT(1) DEFAULT 0,
  `rf_stress`         TINYINT(1) DEFAULT 0,
  `rf_catheter`       TINYINT(1) DEFAULT 0,
  -- Vitals
  `v_temperature`     DECIMAL(4,1) DEFAULT NULL,
  `v_heart_rate`      SMALLINT    DEFAULT NULL,
  `v_systolic_bp`     SMALLINT    DEFAULT NULL,
  `v_diastolic_bp`    SMALLINT    DEFAULT NULL,
  `v_spo2`            TINYINT     DEFAULT NULL,
  `v_resp_rate`       TINYINT     DEFAULT NULL,
  `v_blood_glucose`   DECIMAL(6,1) DEFAULT NULL,
  -- Symptoms (stored as JSON array of symptom IDs)
  `symptoms_json`     JSON        NOT NULL,
  -- AI results
  `predictions_json`  JSON        NOT NULL,
  `triage_level`      ENUM('critical','high','moderate','low','inconclusive') NOT NULL DEFAULT 'inconclusive',
  `top_diagnosis`     VARCHAR(120) DEFAULT NULL,
  `top_confidence`    TINYINT UNSIGNED DEFAULT NULL,
  -- Metadata
  `notes`             TEXT        DEFAULT NULL,
  `created_at`        DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_assess_patient` (`patient_id`),
  KEY `fk_assess_user`    (`assessed_by`),
  CONSTRAINT `fk_assess_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_assess_user`    FOREIGN KEY (`assessed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────────
-- Table: sessions  (server-managed session tokens)
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `sessions` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `token`       VARCHAR(64)  NOT NULL UNIQUE,
  `ip_address`  VARCHAR(45)  DEFAULT NULL,
  `user_agent`  VARCHAR(255) DEFAULT NULL,
  `expires_at`  DATETIME     NOT NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sessions_user` (`user_id`),
  CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────────
-- View: assessment_summary  (handy for history page)
-- ──────────────────────────────────────────────────────────────
CREATE OR REPLACE VIEW `v_assessment_summary` AS
SELECT
  a.id                AS assessment_id,
  p.id                AS patient_id,
  p.mrn,
  p.full_name         AS patient_name,
  p.age,
  p.gender,
  p.ward,
  a.triage_level,
  a.top_diagnosis,
  a.top_confidence,
  a.created_at        AS assessed_at,
  u.full_name         AS assessed_by_name,
  u.role              AS assessor_role,
  JSON_LENGTH(a.symptoms_json) AS symptom_count,
  JSON_LENGTH(a.predictions_json) AS prediction_count
FROM assessments a
JOIN patients    p ON p.id = a.patient_id
LEFT JOIN users  u ON u.id = a.assessed_by
ORDER BY a.created_at DESC;
