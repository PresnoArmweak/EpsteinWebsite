-- ============================================================
-- SOURCES-ONLY SYNC FILE
--
-- Use this file to keep the `sources` table identical between
-- your local XAMPP database and the live cPanel database.
-- Import it into whichever database you want to bring in sync.
-- It DROPS and re-creates the sources table, then re-inserts
-- the master list. It does not touch any other table.
--
-- WORKFLOW:
--
-- A. To go LIVE -> LOCAL (most common):
--    1. On the live site, in phpMyAdmin, select the `sources`
--       table and click Export -> "Custom" -> uncheck everything
--       except "Add DROP TABLE / VIEW" and "Add CREATE TABLE",
--       and the sources table itself. Download the .sql file.
--    2. Replace this file's contents with what you exported.
--    3. Import into your local epstein_archive_local DB.
--
-- B. To go LOCAL -> LIVE:
--    1. Same export, but from your local DB.
--    2. Be very careful: DROP TABLE will erase the live sources
--       table before re-inserting. Take a backup first.
--
-- C. To use this file as-is:
--    1. Open phpMyAdmin
--    2. Select the target database (epstein_archive_local on
--       XAMPP, epsteina_archive on Stablepoint)
--    3. Import this file
--
-- The starter sources below match those in xampp_setup.sql and
-- migrations/001_add_sources.sql. Update one place, copy the
-- inserts to the others, and re-import everywhere.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `sources`;
CREATE TABLE `sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum(
      'web','periodical','book','court_filing',
      'gov_doc','film','interview','other'
  ) NOT NULL DEFAULT 'web',
  `authors` varchar(255) DEFAULT NULL,
  `title` varchar(512) NOT NULL,
  `container` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `date_published` varchar(64) DEFAULT NULL,
  `url` varchar(1024) DEFAULT NULL,
  `date_accessed` varchar(64) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sources`
(`id`, `type`, `authors`, `title`, `container`, `publisher`,
 `date_published`, `url`, `date_accessed`, `location`, `notes`)
VALUES

(1, 'court_filing', NULL,
 'United States v. Jeffrey Epstein, Indictment',
 'United States District Court, Southern District of New York',
 NULL, '2 July 2019',
 'https://www.justice.gov/usao-sdny/press-release/file/1180816/dl',
 NULL, 'Case No. 1:19-cr-00490', NULL),

(2, 'court_filing', NULL,
 'United States v. Ghislaine Maxwell, Superseding Indictment',
 'United States District Court, Southern District of New York',
 NULL, '29 Mar. 2021',
 'https://www.justice.gov/usao-sdny/press-release/file/1380706/dl',
 NULL, 'Case No. 1:20-cr-00330', NULL),

(3, 'periodical', 'Brown, Julie K.',
 'Perversion of Justice',
 'Miami Herald', NULL, '28 Nov. 2018',
 'https://www.miamiherald.com/news/local/article220097825.html',
 NULL, NULL, 'Three-part investigative series'),

(4, 'court_filing', NULL,
 'Non-Prosecution Agreement',
 'United States Attorney''s Office, Southern District of Florida',
 NULL, '24 Sept. 2007',
 NULL, NULL, NULL,
 'The 2007/2008 NPA signed under U.S. Attorney Alexander Acosta'),

(5, 'gov_doc', NULL,
 'Report on the Department of Justice''s Handling of the Jeffrey Epstein Investigation',
 'Office of Professional Responsibility, U.S. Department of Justice',
 NULL, 'Nov. 2020',
 NULL, NULL, NULL, NULL);
