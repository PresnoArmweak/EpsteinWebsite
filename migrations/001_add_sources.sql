-- ============================================================
-- Migration: add the sources table for the citation system
-- Run this ONCE in phpMyAdmin against the existing
-- epsteina_archive database. It does not touch any existing
-- table or data.
-- ============================================================

DROP TABLE IF EXISTS `sources`;
CREATE TABLE `sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum(
      'web',
      'periodical',
      'book',
      'court_filing',
      'gov_doc',
      'film',
      'interview',
      'other'
  ) NOT NULL DEFAULT 'web',
  `authors` varchar(255) DEFAULT NULL
      COMMENT 'MLA-style: "Last, First" or "Last, First, et al."',
  `title` varchar(512) NOT NULL
      COMMENT 'Short work title (in quotes in MLA)',
  `container` varchar(255) DEFAULT NULL
      COMMENT 'Larger work it appears in: newspaper, journal, book, court system',
  `publisher` varchar(255) DEFAULT NULL,
  `date_published` varchar(64) DEFAULT NULL
      COMMENT 'Free-text: "28 Nov. 2018" or "2016" — keep MLA punctuation',
  `url` varchar(1024) DEFAULT NULL,
  `date_accessed` varchar(64) DEFAULT NULL
      COMMENT 'For web sources only',
  `location` varchar(255) DEFAULT NULL
      COMMENT 'Page range, case number, archive ID, etc.',
  `notes` text DEFAULT NULL
      COMMENT 'Editor notes — never displayed publicly',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- A few starter sources commonly referenced in the public
-- record. These are real, citable items; replace or extend as
-- you go. The number you'll use in entry text is the id column
-- of the inserted row.
-- ============================================================

INSERT INTO `sources`
(`type`, `authors`, `title`, `container`, `publisher`,
 `date_published`, `url`, `date_accessed`, `location`, `notes`)
VALUES

-- 1
('court_filing', NULL,
 'United States v. Jeffrey Epstein, Indictment',
 'United States District Court, Southern District of New York',
 NULL, '2 July 2019',
 'https://www.justice.gov/usao-sdny/press-release/file/1180816/dl',
 NULL, 'Case No. 1:19-cr-00490', NULL),

-- 2
('court_filing', NULL,
 'United States v. Ghislaine Maxwell, Superseding Indictment',
 'United States District Court, Southern District of New York',
 NULL, '29 Mar. 2021',
 'https://www.justice.gov/usao-sdny/press-release/file/1380706/dl',
 NULL, 'Case No. 1:20-cr-00330', NULL),

-- 3
('periodical', 'Brown, Julie K.',
 'Perversion of Justice',
 'Miami Herald', NULL, '28 Nov. 2018',
 'https://www.miamiherald.com/news/local/article220097825.html',
 NULL, NULL, 'Three-part investigative series'),

-- 4
('court_filing', NULL,
 'Non-Prosecution Agreement',
 'United States Attorney''s Office, Southern District of Florida',
 NULL, '24 Sept. 2007',
 NULL, NULL, NULL,
 'The 2007/2008 NPA signed under U.S. Attorney Alexander Acosta'),

-- 5
('gov_doc', NULL,
 'Report on the Department of Justice''s Handling of the Jeffrey Epstein Investigation',
 'Office of Professional Responsibility, U.S. Department of Justice',
 NULL, 'Nov. 2020',
 NULL, NULL, NULL, NULL);
