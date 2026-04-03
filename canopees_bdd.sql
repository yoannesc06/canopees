CREATE DATABASE IF NOT EXISTS `canopees`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `canopees`;

-- demande
CREATE TABLE IF NOT EXISTS `demandes_contact` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `prenom`      VARCHAR(100)  NOT NULL,
  `nom`         VARCHAR(100)  NOT NULL,
  `email`       VARCHAR(255)  NOT NULL,
  `telephone`   VARCHAR(30)   DEFAULT NULL,
  `prestation`  VARCHAR(150)  DEFAULT NULL,
  `message`     TEXT          NOT NULL,
  `date_envoi`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut`      ENUM('nouveau','en_cours','traite','archive')
                              NOT NULL DEFAULT 'nouveau',
  `notes_admin` TEXT          DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_statut`     (`statut`),
  INDEX `idx_date_envoi` (`date_envoi`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- admin
CREATE OR REPLACE VIEW `v_demandes_recentes` AS
  SELECT
    `id`,
    `prenom`,
    `nom`,
    `email`,
    `telephone`,
    `prestation`,
    LEFT(`message`, 120) AS `message_apercu`,
    `date_envoi`,
    `statut`
  FROM `demandes_contact`
  ORDER BY `date_envoi` DESC;
