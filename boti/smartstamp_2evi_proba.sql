-- =====================================================================
--  SmartStamp – Digitális Hűségprogram
--  Adatbázis: 2evi_proba   (MariaDB 10.4+ / XAMPP)
--
--  Importálás: phpMyAdmin -> 2evi_proba -> Importálás -> ez a fájl
--  A fájl TÖRLI és újra létrehozza a táblákat, majd visszatölti a
--  meglévő adatokat (felhasználók, boltok, előfizetések, kedvencek).
--
--  A csapattársak fájljai által használt oszlopnevek változatlanok
--  (users.*, stores.name/address/contact_*, favorites.storeid/userid,
--  user_subscriptions.userid), így azok a fájlok továbbra is működnek.
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = "+01:00";
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `2evi_proba`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;
USE `2evi_proba`;

-- Régi / átnevezett táblák törlése
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `point_transactions`;
DROP TABLE IF EXISTS `store_messages`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `milestones`;
DROP TABLE IF EXISTS `loyalty_cards`;
DROP TABLE IF EXISTS `store_customers`;
DROP TABLE IF EXISTS `store_staff`;
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `stores`;
DROP TABLE IF EXISTS `store_groups`;
DROP TABLE IF EXISTS `user_subscriptions`;
DROP TABLE IF EXISTS `pricing`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- Felhasználók (vásárlók és bolttulajdonosok is)
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`       VARCHAR(255) NOT NULL,
  `last_name`   VARCHAR(100) NOT NULL,
  `first_name`  VARCHAR(100) NOT NULL,
  `password`    VARCHAR(255) NOT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Előfizetési csomagok (NULL = korlátlan)
-- ---------------------------------------------------------------------
CREATE TABLE `pricing` (
  `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                    VARCHAR(100) NOT NULL,
  `price`                   DECIMAL(10,0) NOT NULL,
  `store_count`             INT UNSIGNED DEFAULT NULL,
  `milestone_limit`         INT UNSIGNED DEFAULT NULL,
  `staff_limit`             INT UNSIGNED DEFAULT NULL,
  `allow_shared_db`         TINYINT(1) NOT NULL DEFAULT 0,
  `allow_events`            TINYINT(1) NOT NULL DEFAULT 0,
  `allow_export`            TINYINT(1) NOT NULL DEFAULT 0,
  `allow_priority_support`  TINYINT(1) NOT NULL DEFAULT 0,
  `tier`                    TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pricing_tier` (`tier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Felhasználó előfizetése (felhasználónként legfeljebb egy)
-- ---------------------------------------------------------------------
CREATE TABLE `user_subscriptions` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid`        INT UNSIGNED NOT NULL,
  `subscription`  INT UNSIGNED NOT NULL,
  `started_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires`       DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_subs_user` (`userid`),
  KEY `idx_subs_plan` (`subscription`),
  CONSTRAINT `fk_subs_user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_subs_plan` FOREIGN KEY (`subscription`) REFERENCES `pricing` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Összekapcsolt boltok csoportja (közös pontadatbázis)
-- ---------------------------------------------------------------------
CREATE TABLE `store_groups` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_userid`  INT UNSIGNED NOT NULL,
  `name`          VARCHAR(150) NOT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_groups_owner` (`owner_userid`),
  CONSTRAINT `fk_groups_owner` FOREIGN KEY (`owner_userid`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Boltok
-- ---------------------------------------------------------------------
CREATE TABLE `stores` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_userid`       INT UNSIGNED NOT NULL,
  `group_id`           INT UNSIGNED DEFAULT NULL,
  `name`               VARCHAR(150) NOT NULL,
  `type`               VARCHAR(60)  NOT NULL,
  `address`            VARCHAR(255) NOT NULL,
  `contact_name`       VARCHAR(150) NOT NULL,
  `contact_email`      VARCHAR(255) NOT NULL,
  `tel`                VARCHAR(30)  NOT NULL,
  `points_per_scan`    INT UNSIGNED NOT NULL DEFAULT 1,
  `scan_cooldown_min`  INT UNSIGNED NOT NULL DEFAULT 5,
  `created_at`         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_stores_owner` (`owner_userid`),
  KEY `idx_stores_group` (`group_id`),
  KEY `idx_stores_name` (`name`),
  CONSTRAINT `fk_stores_owner` FOREIGN KEY (`owner_userid`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stores_group` FOREIGN KEY (`group_id`) REFERENCES `store_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_points_per_scan` CHECK (`points_per_scan` BETWEEN 1 AND 1000)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Kedvenc boltok (oszlopnevek a meglévő kód miatt maradtak)
-- ---------------------------------------------------------------------
CREATE TABLE `favorites` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `storeid`     INT UNSIGNED NOT NULL,
  `userid`      INT UNSIGNED NOT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fav_user_store` (`userid`, `storeid`),
  KEY `idx_fav_store` (`storeid`),
  CONSTRAINT `fk_fav_store` FOREIGN KEY (`storeid`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fav_user`  FOREIGN KEY (`userid`)  REFERENCES `users`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Munkatársak és jogosultságok (rangrendszer)
-- A tulajdonos (stores.owner_userid) mindig minden joggal rendelkezik.
-- ---------------------------------------------------------------------
CREATE TABLE `store_staff` (
  `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id`               INT UNSIGNED NOT NULL,
  `user_id`                INT UNSIGNED NOT NULL,
  `role`                   VARCHAR(30)  NOT NULL DEFAULT 'pultos',
  `can_scan`               TINYINT(1)   NOT NULL DEFAULT 1,
  `can_adjust_points`      TINYINT(1)   NOT NULL DEFAULT 0,
  `can_manage_milestones`  TINYINT(1)   NOT NULL DEFAULT 0,
  `can_manage_events`      TINYINT(1)   NOT NULL DEFAULT 0,
  `can_manage_messages`    TINYINT(1)   NOT NULL DEFAULT 0,
  `can_manage_staff`       TINYINT(1)   NOT NULL DEFAULT 0,
  `can_manage_settings`    TINYINT(1)   NOT NULL DEFAULT 0,
  `can_delete`             TINYINT(1)   NOT NULL DEFAULT 0,
  `added_by`               INT UNSIGNED DEFAULT NULL,
  `created_at`             TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_staff_store_user` (`store_id`, `user_id`),
  KEY `idx_staff_user` (`user_id`),
  CONSTRAINT `fk_staff_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_added` FOREIGN KEY (`added_by`) REFERENCES `users`  (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Hűségkártyák: egy vásárló pontjai egy boltban VAGY egy boltcsoportban.
-- Pontosan az egyik (store_id / group_id) van kitöltve.
-- qr_token: a vásárló egyedi QR kódjának tartalma (boltonként más).
-- ---------------------------------------------------------------------
CREATE TABLE `loyalty_cards` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       INT UNSIGNED NOT NULL,
  `store_id`      INT UNSIGNED DEFAULT NULL,
  `group_id`      INT UNSIGNED DEFAULT NULL,
  `qr_token`      CHAR(32)     NOT NULL,
  `points`        INT          NOT NULL DEFAULT 0,
  `total_earned`  INT UNSIGNED NOT NULL DEFAULT 0,
  `last_scan_at`  DATETIME     DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cards_token` (`qr_token`),
  UNIQUE KEY `uq_cards_user_store` (`user_id`, `store_id`),
  UNIQUE KEY `uq_cards_user_group` (`user_id`, `group_id`),
  KEY `idx_cards_store` (`store_id`),
  KEY `idx_cards_group` (`group_id`),
  CONSTRAINT `fk_cards_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`        (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cards_store` FOREIGN KEY (`store_id`) REFERENCES `stores`       (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cards_group` FOREIGN KEY (`group_id`) REFERENCES `store_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_cards_scope` CHECK ((`store_id` IS NULL) <> (`group_id` IS NULL)),
  CONSTRAINT `chk_cards_points` CHECK (`points` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Pont mérföldkövek + személyre szabott üzenet mérföldkövenként
-- (boltra VAGY boltcsoportra vonatkozik, mint a kártyák)
-- ---------------------------------------------------------------------
CREATE TABLE `milestones` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id`            INT UNSIGNED DEFAULT NULL,
  `group_id`            INT UNSIGNED DEFAULT NULL,
  `title`               VARCHAR(100) NOT NULL,
  `points_required`     INT UNSIGNED NOT NULL,
  `reward_description`  VARCHAR(255) NOT NULL,
  `customer_message`    VARCHAR(500) DEFAULT NULL,
  `cashier_message`     VARCHAR(500) DEFAULT NULL,
  `is_active`           TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ms_store` (`store_id`, `is_active`, `points_required`),
  KEY `idx_ms_group` (`group_id`, `is_active`, `points_required`),
  CONSTRAINT `fk_ms_store` FOREIGN KEY (`store_id`) REFERENCES `stores`       (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ms_group` FOREIGN KEY (`group_id`) REFERENCES `store_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_ms_scope` CHECK ((`store_id` IS NULL) <> (`group_id` IS NULL)),
  CONSTRAINT `chk_ms_points` CHECK (`points_required` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Események / akciók (pl. dupla pontok napja) – boltonként
-- ---------------------------------------------------------------------
CREATE TABLE `events` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id`      INT UNSIGNED NOT NULL,
  `title`         VARCHAR(100) NOT NULL,
  `description`   VARCHAR(500) DEFAULT NULL,
  `multiplier`    TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `bonus_points`  INT UNSIGNED NOT NULL DEFAULT 0,
  `starts_at`     DATETIME     NOT NULL,
  `ends_at`       DATETIME     NOT NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_by`    INT UNSIGNED DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_events_store_time` (`store_id`, `is_active`, `starts_at`, `ends_at`),
  CONSTRAINT `fk_events_store` FOREIGN KEY (`store_id`)   REFERENCES `stores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_events_user`  FOREIGN KEY (`created_by`) REFERENCES `users`  (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_events_time` CHECK (`ends_at` > `starts_at`),
  CONSTRAINT `chk_events_mult` CHECK (`multiplier` BETWEEN 1 AND 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Egyedi üzenetek pontgyűjtéskor (pultosnak / vásárlónak)
-- Helyettesítők: {nev} {pont} {bolt} {kapott}
-- ---------------------------------------------------------------------
CREATE TABLE `store_messages` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id`      INT UNSIGNED NOT NULL,
  `trigger_type`  ENUM('first_scan','every_scan') NOT NULL DEFAULT 'every_scan',
  `audience`      ENUM('customer','cashier')      NOT NULL DEFAULT 'customer',
  `message`       VARCHAR(500) NOT NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_msg_store` (`store_id`, `is_active`, `trigger_type`),
  CONSTRAINT `fk_msg_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Ponttörténet (minden pontváltozás naplózva – statisztikákhoz is)
-- ---------------------------------------------------------------------
CREATE TABLE `point_transactions` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `card_id`        INT UNSIGNED NOT NULL,
  `store_id`       INT UNSIGNED DEFAULT NULL,
  `staff_user_id`  INT UNSIGNED DEFAULT NULL,
  `type`           ENUM('scan','manual','redeem') NOT NULL,
  `points_change`  INT          NOT NULL,
  `balance_after`  INT          NOT NULL,
  `event_id`       INT UNSIGNED DEFAULT NULL,
  `milestone_id`   INT UNSIGNED DEFAULT NULL,
  `note`           VARCHAR(255) DEFAULT NULL,
  `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tx_card` (`card_id`, `created_at`),
  KEY `idx_tx_store_time` (`store_id`, `created_at`),
  CONSTRAINT `fk_tx_card`  FOREIGN KEY (`card_id`)       REFERENCES `loyalty_cards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tx_store` FOREIGN KEY (`store_id`)      REFERENCES `stores`        (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_tx_staff` FOREIGN KEY (`staff_user_id`) REFERENCES `users`         (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_tx_event` FOREIGN KEY (`event_id`)      REFERENCES `events`        (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_tx_ms`    FOREIGN KEY (`milestone_id`)  REFERENCES `milestones`    (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- ---------------------------------------------------------------------
-- Értesítések a vásárlónak (pl. elért mérföldkő, egyedi üzenet)
-- ---------------------------------------------------------------------
CREATE TABLE `notifications` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `store_id`    INT UNSIGNED DEFAULT NULL,
  `message`     VARCHAR(500) NOT NULL,
  `is_read`     TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`, `is_read`, `created_at`),
  CONSTRAINT `fk_notif_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_notif_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- =====================================================================
--  MEGLÉVŐ ADATOK VISSZATÖLTÉSE
--  Az élő adatbázisban Botond id-je 2 volt, a boltok (4-10) és az
--  előfizetés viszont a már nem létező 1-es felhasználóra mutattak –
--  ezek most Botondhoz (id 2) kerültek. Konrad a régi dumpból jön
--  vissza 3-as id-vel (a kedvencei is).
-- =====================================================================

INSERT INTO `users` (`id`, `email`, `last_name`, `first_name`, `password`, `created_at`) VALUES
(2, 'bagoboti@gmail.com',  'Bagó',   'Botond', '$2y$10$jLCM0ibkEfrCzX7w9w/FpehscCC/H0D2Qo46qRr4BiI8i3P553DDq', '2026-11-15 14:49:27'),
(3, 'bkoni2008@gmail.com', 'Bordas', 'Konrad', '$2y$10$Z85TZexkGUWh2G2GmabRReSzCFWNFU2HtPD4vMpSEKrOJZABLJntS', '2026-11-02 21:00:00');

INSERT INTO `pricing` (`id`, `name`, `price`, `store_count`, `milestone_limit`, `staff_limit`, `allow_shared_db`, `allow_events`, `allow_export`, `allow_priority_support`, `tier`) VALUES
(6, 'Kezdő',   1000,  1,    3,    2,    0, 0, 0, 0, 1),
(7, 'Haladó',  4990,  5,    NULL, 10,   1, 1, 0, 0, 2),
(8, 'Üzleti',  12990, NULL, NULL, NULL, 1, 1, 1, 1, 3);

INSERT INTO `user_subscriptions` (`id`, `userid`, `subscription`, `started_at`, `expires`) VALUES
(4, 2, 7, '2026-11-04 19:00:47', '2026-12-04 19:00:47'),
(5, 3, 7, '2026-11-02 19:58:08', '2026-12-02 19:58:08');

INSERT INTO `stores` (`id`, `owner_userid`, `group_id`, `name`, `type`, `address`, `contact_name`, `contact_email`, `tel`, `points_per_scan`, `scan_cooldown_min`) VALUES
(4,   2, NULL, 'Starbucks',      'Kávézó',    'Budapest, Pesti út 12, 1172',      'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5),
(5,   2, NULL, 'Lipóti Pékség',  'Pékség',    'Budapest, Ferihegyi út 64, 1173',  'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5),
(6,   2, NULL, 'Bio Hair',       'Fodrászat', 'Budapest, Budapesti út 24, 1164',  'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5),
(8,   2, NULL, 'Lipóti Pékség',  'Kávézó',    'Budapest, Pesti út 12, 1172',      'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5),
(10,  2, NULL, 'Domi Pizzázója', 'Pizzázó',   'Budapest, Budapesti út 24, 1164',  'Kertész Dominik',     'keridomi@gmail.com', '+36309122307', 1, 5),
(11,  2, NULL, 'Lipóti Pékség',  'Pékség',    'Budapest, Pesti út 12, 1172',      'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5);

INSERT INTO `favorites` (`id`, `storeid`, `userid`) VALUES
(22, 4, 3),
(23, 5, 3);

-- =====================================================================
--  DEMO ADATOK (bemutatáshoz – nyugodtan törölhető)
--  Starbucks (id 4): mérföldkövek, üzenet, Konrad hűségkártyája (0 pont)
-- =====================================================================

INSERT INTO `milestones` (`store_id`, `title`, `points_required`, `reward_description`, `customer_message`, `cashier_message`) VALUES
(4, 'Ingyen kávé',     10, 'Egy tetszőleges kávé ingyen',  'Gratulálunk {nev}! Elérted a {pont} pontot, a következő kávéd ajándék!', 'Jár egy ingyen kávé a vásárlónak!'),
(4, 'Kávé + sütemény', 20, 'Egy kávé és egy sütemény ingyen', 'Szuper {nev}! Kávé és sütemény a miénk!', 'Kávé + sütemény jár!');

INSERT INTO `store_messages` (`store_id`, `trigger_type`, `audience`, `message`) VALUES
(4, 'first_scan', 'customer', 'Üdv a {bolt} hűségprogramjában, {nev}!'),
(4, 'first_scan', 'cashier',  'Új törzsvásárló: {nev}');

INSERT INTO `loyalty_cards` (`id`, `user_id`, `store_id`, `group_id`, `qr_token`, `points`, `total_earned`, `last_scan_at`) VALUES
(1, 3, 4, NULL, 'a3f1c9e07b5d4e2f8c6a1b0d9e7f3c21', 0, 0, NULL);

-- AUTO_INCREMENT értékek a meglévő id-k után
ALTER TABLE `users`              AUTO_INCREMENT = 4;
ALTER TABLE `pricing`            AUTO_INCREMENT = 9;
ALTER TABLE `user_subscriptions` AUTO_INCREMENT = 6;
ALTER TABLE `stores`             AUTO_INCREMENT = 12;
ALTER TABLE `favorites`          AUTO_INCREMENT = 24;
