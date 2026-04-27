-- phpMyAdmin SQL Dump
-- Database for the Epstein Archive wiki
-- Import via phpMyAdmin in XAMPP, or run from the mysql CLI.
--
-- This dump contains structure + stub rows.
-- Each "entry" body is intentionally left empty for the editor to write.
-- The stubs only carry name, dates (where part of the public record), and
-- a one-line "connection" that describes how the person appears in the
-- public Epstein-case record. No biographical claims are made here.

CREATE DATABASE IF NOT EXISTS `epstein_archive` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `epstein_archive`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- users
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `full_name` varchar(128) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','member') NOT NULL DEFAULT 'member',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- categories
-- --------------------------------------------------------

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- figures (one entry per person/entity)
-- --------------------------------------------------------

DROP TABLE IF EXISTS `figures`;
CREATE TABLE `figures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `category_id` int(11) NOT NULL,
  `lifespan` varchar(64) DEFAULT NULL,
  `nationality` varchar(64) DEFAULT NULL,
  `known_for` varchar(255) DEFAULT NULL,
  `summary` text NOT NULL,
  `biography` mediumtext NOT NULL,
  `is_restricted` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin_only` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_figures_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed: users
--   admin  / admin123
--   member / member123
-- (hashes generated with php password_hash($pw, PASSWORD_DEFAULT))
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `username`, `full_name`, `password_hash`, `role`) VALUES
(1, 'admin',  'Archive Administrator', '$2y$10$mxVi0XTEi00PmqE7S7uSmexFa7SOOMcRbjVBv8OSRGyvkGvb7MGoO', 'admin'),
(2, 'member', 'Registered Reader',     '$2y$10$lNCOAKeT0I.SPssYx2vzGOcDKlGqTvamb/gxBzXjBSyHGzCK9rr2S', 'member');

-- --------------------------------------------------------
-- Seed: categories
-- --------------------------------------------------------

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Principals',   'principals',   'The convicted and the directly charged.'),
(2, 'Associates',   'associates',   'Friends, business partners, staff, and frequent contacts named in the public record.'),
(3, 'Accusers',     'accusers',     'Individuals who have publicly identified themselves as victims and given testimony or filed claims.'),
(4, 'Officials',    'officials',    'Prosecutors, judges, investigators, and government figures whose decisions shaped the case.'),
(5, 'Institutions', 'institutions', 'Banks, schools, properties, charities, and organizations referenced in the record.'),
(6, 'Other',        'other',        'Witnesses, journalists, and other notable persons whose names appear in case materials.');

-- --------------------------------------------------------
-- Seed: figures (stubs only — bodies are blank for the editor)
--
-- Each row carries:
--   - name           (as it appears in court documents / mainstream press)
--   - slug           (URL-safe version)
--   - category_id    (which category this entry belongs to)
--   - lifespan       (only filled where it is part of the public record)
--   - nationality    (left NULL — fill in as you write each entry)
--   - known_for      (one neutral line: "how this person appears in the case")
--   - summary        (one neutral sentence; you can rewrite it)
--   - biography      ("" — you write the full entry yourself)
--   - is_restricted  (0 = public, 1 = members only)
--   - is_admin_only  (1 = admin dashboard only; used here for an
--                       internal "editorial notes" placeholder)
-- --------------------------------------------------------

INSERT INTO `figures`
(`id`, `name`, `slug`, `category_id`, `lifespan`, `nationality`, `known_for`,
 `summary`, `biography`, `is_restricted`, `is_admin_only`) VALUES

-- Principals -----------------------------------------------------------------
(1, 'Jeffrey Epstein', 'jeffrey-epstein', 1, '1953 – 2019', NULL,
 'Convicted sex offender; 2019 federal indictment for sex trafficking minors.',
 'Stub entry. Replace this summary with your own neutral one-line description.',
 '', 0, 0),

(2, 'Ghislaine Maxwell', 'ghislaine-maxwell', 1, '1961 –', NULL,
 'Convicted in 2021 on five federal counts including sex trafficking of minors.',
 'Stub entry. Replace this summary with your own neutral one-line description.',
 '', 0, 0),

-- Associates ----------------------------------------------------------------
(3, 'Associate Stub A', 'associate-stub-a', 2, NULL, NULL,
 'Named in publicly released flight logs and/or court filings.',
 'Stub entry. Rename this row and write the entry. Set is_restricted = 1 if you want it members-only.',
 '', 0, 0),

(4, 'Associate Stub B', 'associate-stub-b', 2, NULL, NULL,
 'Named in publicly released flight logs and/or court filings.',
 'Stub entry. Rename and fill in.',
 '', 0, 0),

(5, 'Associate Stub C', 'associate-stub-c', 2, NULL, NULL,
 'Named in publicly released flight logs and/or court filings.',
 'Stub entry. Rename and fill in.',
 '', 0, 0),

-- Accusers ------------------------------------------------------------------
-- These stubs are intentionally generic. Use real names only for accusers
-- who have publicly self-identified (e.g. through filed lawsuits, on-record
-- interviews, or signed affidavits). For accusers who remain anonymous in
-- the public record (Jane/John Doe filings), keep them anonymous here.
(6, 'Accuser Stub A', 'accuser-stub-a', 3, NULL, NULL,
 'Self-identified accuser; testimony and/or civil filing in the public record.',
 'Stub entry. Replace name only if the person has self-identified publicly.',
 '', 0, 0),

(7, 'Accuser Stub B', 'accuser-stub-b', 3, NULL, NULL,
 'Self-identified accuser; testimony and/or civil filing in the public record.',
 'Stub entry. Replace name only if the person has self-identified publicly.',
 '', 0, 0),

(8, 'Anonymous Doe Plaintiffs', 'doe-plaintiffs', 3, NULL, NULL,
 'Plaintiffs filing under pseudonyms (Jane Doe / John Doe) in civil suits.',
 'Stub entry. This row is for an aggregate overview — do not name individuals who have not self-identified.',
 '', 1, 0),

-- Officials -----------------------------------------------------------------
(9, 'Alexander Acosta', 'alexander-acosta', 4, NULL, NULL,
 'Then-U.S. Attorney for the Southern District of Florida; signed the 2008 non-prosecution agreement.',
 'Stub entry. Fill in.',
 '', 0, 0),

(10, 'SDNY Prosecution Team', 'sdny-prosecution-team', 4, NULL, NULL,
 'Federal prosecutors who brought the 2019 sex trafficking indictment in the Southern District of New York.',
 'Stub entry. Fill in.',
 '', 0, 0),

(11, 'Judge Stub', 'judge-stub', 4, NULL, NULL,
 'Presiding or sentencing judge in one of the related federal proceedings.',
 'Stub entry. Rename and fill in.',
 '', 0, 0),

-- Institutions --------------------------------------------------------------
(12, 'Little Saint James', 'little-saint-james', 5, NULL, NULL,
 'Private island in the U.S. Virgin Islands owned by Epstein.',
 'Stub entry. Fill in.',
 '', 0, 0),

(13, 'Zorro Ranch', 'zorro-ranch', 5, NULL, NULL,
 'Property in Stanley, New Mexico, owned by Epstein.',
 'Stub entry. Fill in.',
 '', 0, 0),

(14, '71st Street Townhouse', 'east-71st-street-townhouse', 5, NULL, NULL,
 'Manhattan residence formerly owned by Epstein.',
 'Stub entry. Fill in.',
 '', 0, 0),

(15, 'Bank Stub', 'bank-stub', 5, NULL, NULL,
 'Financial institution that held accounts associated with Epstein and faced subsequent settlements.',
 'Stub entry. Rename and fill in.',
 '', 0, 0),

(16, 'Charity / Foundation Stub', 'foundation-stub', 5, NULL, NULL,
 'Foundation or donor-advised fund used by Epstein for charitable giving.',
 'Stub entry. Rename and fill in.',
 '', 0, 0),

-- Other ---------------------------------------------------------------------
(17, 'Investigative Reporter Stub', 'reporter-stub', 6, NULL, NULL,
 'Journalist whose reporting materially advanced the public record on this case.',
 'Stub entry. Rename and fill in.',
 '', 0, 0),

(18, 'Witness Stub', 'witness-stub', 6, NULL, NULL,
 'Trial or grand-jury witness named in unsealed transcripts.',
 'Stub entry. Rename and fill in.',
 '', 0, 0),

-- Editorial admin-only entry ------------------------------------------------
(19, 'Editorial Notes', 'editorial-notes', 6, NULL, NULL,
 'Internal sourcing standards and style guide.',
 'Admin-only placeholder. Use this entry to keep your sourcing rules visible only to admins.',
 'Suggested editorial standards for this archive (replace with your own):

1. Cite primary sources (court filings, DOJ releases, sworn depositions, official congressional records) before secondary reporting.

2. Distinguish carefully between three categories of fact: (a) judicial findings, (b) sworn testimony, and (c) reporting and allegation. Use language that signals which category each statement belongs to.

3. Do not name accusers who have not self-identified publicly. Use "Jane/John Doe" designations as the court did.

4. Being named in flight logs, address books, or photographs is not, on its own, evidence of wrongdoing. State the underlying fact precisely; do not let layout or proximity imply guilt.

5. Update entries when new primary documents are released; mark major revisions with a date.', 1, 1);
