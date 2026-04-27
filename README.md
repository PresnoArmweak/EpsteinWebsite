# The Epstein Archive

A read-only reference of people and institutions named in the public record
surrounding the Jeffrey Epstein case. Each entry covers one person or
institution; entries are organized by role.

The site is intentionally read-only: there is **no public editing** and no
public registration. Entries are curated and updated by editors directly in
the database.

## Stack

- PHP 8 (procedural front-controller in `index.php`)
- MySQL / MariaDB (via XAMPP)
- Bootstrap 5 grid + a custom case-file stylesheet

## Folder layout

```
epstein-archive/
├── assets/
│   └── styles.css
├── components/
│   ├── nav.php
│   ├── sidebar.php
│   └── footer.php
├── data/
│   ├── db.php
│   └── functions.php
├── partials/
│   ├── home.php
│   ├── category.php
│   ├── article.php
│   ├── search.php
│   ├── login_form.php
│   ├── denied.php
│   ├── admin.php
│   └── about.php
├── epstein_archive.sql
├── index.php
└── README.md
```

## Setup (XAMPP)

1. Drop the folder into `xampp/htdocs/`.
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open <http://localhost/phpmyadmin>.
4. **Import** &rarr; choose `epstein_archive.sql` &rarr; **Go**.
5. Visit <http://localhost/epstein-archive/> (or whatever folder name you
   used).

If your MySQL `root` user has a password, edit `data/db.php` and set
`$pass`.

## Access tiers

| Tier   | Sees                                                    |
|--------|---------------------------------------------------------|
| Guest  | public entries only                                     |
| Member | public + members-only entries                           |
| Admin  | everything, plus the read-only editorial dashboard      |

### Demo accounts

| Username | Password    | Role   |
|----------|-------------|--------|
| `admin`  | `admin123`  | admin  |
| `member` | `member123` | member |

Change them on any deployment.

## Stub entries

The SQL dump seeds **categories** and **stub rows** (a name, a one-line
"connection," and an empty body). The bodies are deliberately blank for the
editor to write. Edit them directly in the `figures` table:

```sql
UPDATE figures
   SET biography = 'Your full entry text here.\n\nNew paragraphs are\nseparated by blank lines.',
       summary   = 'A neutral one-line description for listings.',
       nationality = 'Optional affiliation/country/role qualifier'
 WHERE slug = 'jeffrey-epstein';
```

To rename or expand a stub:

```sql
UPDATE figures
   SET name = 'Real Name',
       slug = 'real-name'
 WHERE slug = 'associate-stub-a';
```

To gate an entry to members-only or admin-only:

```sql
UPDATE figures SET is_restricted = 1 WHERE slug = '...';   -- members only
UPDATE figures SET is_admin_only = 1 WHERE slug = '...';   -- admin only
```

To add a brand-new entry:

```sql
INSERT INTO figures
  (name, slug, category_id, lifespan, nationality, known_for,
   summary, biography, is_restricted, is_admin_only)
VALUES
  ('Some Person', 'some-person', 2, NULL, NULL,
   'How they appear in the public record',
   'One-line summary used in listings and cards.',
   'Multi-paragraph body. Use blank lines between paragraphs.',
   0, 0);
```

## Suggested editorial standards

A starter set is included as an admin-only entry called **Editorial Notes**.
Replace it with your own. Suggested baselines:

1. **Cite primary sources first.** Court filings, DOJ releases, sworn
   depositions, and official congressional records before secondary
   reporting.
2. **Distinguish judicial findings, sworn testimony, and reporting/
   allegation.** Use language that signals which category each statement
   belongs to.
3. **Don&rsquo;t name unidentified accusers.** Use Jane/John Doe
   designations as the courts did.
4. **Being named is not, alone, evidence of wrongdoing.** Where the only
   basis for inclusion is a flight log, an address book, or a photograph,
   say so plainly.
5. **Date major revisions.** When a new primary document changes an entry,
   note the date inside the body.
