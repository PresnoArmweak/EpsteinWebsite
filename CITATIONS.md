# Citation system — editor guide

## Concept

Every source the archive cites lives **once** in the `sources` table, with
a permanent ID number. That number is what you use in any entry to cite
that source. Source #47 means the same NYT article on every page that
cites it.

Numbers come from the `sources.id` column — they're auto-assigned when
you `INSERT` and never change. The `/sources` page lists them all in MLA 9
format, in numeric order.

## Adding a new source

In phpMyAdmin → `epsteina_archive` → `sources` table → **Insert** tab,
or via the SQL tab:

```sql
INSERT INTO sources
  (type, authors, title, container, publisher, date_published, url,
   date_accessed, location, notes)
VALUES
  ('periodical',                        -- type (see list below)
   'Brown, Julie K.',                   -- MLA: Last, First (or Last, First, et al.)
   'Perversion of Justice',             -- the work itself
   'Miami Herald',                      -- the larger work it appears in
   NULL,                                -- publisher (often = container for newspapers)
   '28 Nov. 2018',                      -- MLA-style date
   'https://www.miamiherald.com/...',   -- url
   NULL,                                -- accessed date (only for web sources)
   NULL,                                -- page/case#/archive id
   'Three-part series'                  -- editor notes; never displayed
  );
```

Then run `SELECT LAST_INSERT_ID();` (or just look at the table) to get
the new ID. That number is the citation key.

### Types

| `type` value     | When to use it                                         |
|------------------|--------------------------------------------------------|
| `web`            | Generic web page, blog post, etc.                      |
| `periodical`     | Newspaper, magazine, journal article                   |
| `book`           | Book                                                   |
| `court_filing`   | Indictment, deposition, motion, NPA, transcript        |
| `gov_doc`        | DOJ report, Senate testimony, agency document          |
| `film`           | Documentary, broadcast interview                       |
| `interview`      | First-person interview given to the editorial team     |
| `other`          | Anything else                                          |

The site auto-formats each type per MLA 9 conventions. Court filings
italicize the case title; books format Author / Title / Publisher / Year;
etc.

### Field tips

- **authors**: `Last, First` for one author. `Last, First, and First Last`
  for two. `Last, First, et al.` for three+. Leave empty if no author
  (e.g. unsigned editorials, court filings, agency reports).
- **title**: short title only. Don't add the source/publisher here.
- **container**: the *larger* work. For an NYT article: `The New York Times`.
  For an essay in a book: the book's title.
- **date_published**: write it in MLA format already so it round-trips: `28 Nov. 2018`,
  `2016`, `Fall 2020`. The site does not parse this — it prints it verbatim.
- **url**: full URL. The site auto-makes it a link.
- **location**: page numbers (`pp. 12-15`), case number (`Case No. 1:19-cr-00490`),
  archive ID (`Box 7, Folder 23`) — type-specific.

## Citing a source in an entry body

In the `figures.biography` text, write the citation marker inline:

| You write              | What renders                            |
|------------------------|-----------------------------------------|
| `[47]`                 | subscript `[47]` linking to source #47  |
| `[47, 12]`             | one bracket, both linked: `[47, 12]`    |
| `[47][12]`             | merged into one: `[47, 12]`             |
| `[47, p. 23]`          | `[47 (p. 23)]` with locator             |
| `[47, p. 23; 12, p. 5]`| two cites, each with its own locator    |
| `[999]` (unknown id)   | rendered red with hover note "not in registry" |

**Disambiguation rule**: inside one bracket, the parser decides if the
text after a comma is a locator or another id by checking whether it's
purely numeric.

- `[47, 12]` → numeric → second id
- `[47, p. 12]` → non-numeric → locator on source #47

If you ever need to cite source #47 with a locator that *starts* with a
digit (e.g. `2nd ed.`), prefix something non-numeric: `[47, ed. 2]` or use
`pp.` form: `[47, pp. 23-29]`.

## Workflow when writing a new entry

1. Skim your notes; identify every distinct source you'll cite.
2. Add each one to the `sources` table that isn't there yet, capture
   their IDs.
3. Write the entry body with `[id]` markers inline.
4. `UPDATE figures SET biography = '...' WHERE slug = '...';`
5. Visit the entry on the live site. Click each citation to verify it
   jumps to the right source on `/sources`.
6. Look for any **red** citation numbers — those mean a typo'd source ID.

## Style guidance

- Cite specific claims, not whole paragraphs. A citation goes at the end
  of the sentence whose claim it supports.
- For a multi-source claim, prefer `[1, 3]` (one bracket) over `[1][3]` —
  both render the same, but the first is easier to read in raw form.
- Use a locator when citing a long document and pointing to a specific
  part: `[5, p. 12]`, `[2, ¶ 14]`, `[10, transcript p. 234]`.
- Don't cite the same source twice in the same sentence; one cite covers
  the whole sentence.
- Court documents over reporting where both are available.
