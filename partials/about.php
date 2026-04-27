<?php // partials/about.php ?>
<header class="historica-page-header">
    <span class="historica-breadcrumb">
        <a href="?view=home">Home</a>
        <span>&rsaquo;</span>
        <span>About</span>
    </span>
    <h1 class="historica-page-header__title">About this archive</h1>
</header>

<div class="historica-prose">
    <p>
        The Epstein Archive is a curated, read-only reference of people and
        institutions named in the public record surrounding the Jeffrey
        Epstein case. Unlike a collaborative wiki, it does not accept public
        edits or new submissions. Entries are written and maintained by the
        editorial team and updated through the underlying database.
    </p>

    <h2>// What this site is</h2>
    <p>
        A reference index. Each entry covers one person or institution and
        links the names found in court filings, depositions, and major
        investigative reporting to a short, sourced overview. It is
        <em>not</em> a document host; the underlying primary materials live
        on the websites of courts, the U.S. Department of Justice, the
        congressional committees that have released them, and established
        news outlets.
    </p>

    <h2>// What this site is not</h2>
    <p>
        Not a verdict, not a list of accusations, and not a tabloid. Being
        named in a flight log, an address book, or a photograph is not, on
        its own, evidence of wrongdoing &mdash; entries say so plainly when
        that is the only basis for inclusion. Allegations are described as
        allegations; convictions as convictions.
    </p>

    <h2>// Access tiers</h2>
    <p>
        Most entries are public. A subset is gated to signed-in members
        &mdash; typically entries that aggregate information about
        unidentified accusers, or that depend heavily on uncorroborated
        reporting. A small number of internal entries (style notes, sourcing
        rules) are visible only to admins. Sidebar counts reflect what
        <em>you</em> can see.
    </p>

    <h2>// How it&rsquo;s organised</h2>
    <p>
        Entries are grouped by role: principals (the convicted and directly
        charged), associates (named in the public record), accusers (those
        who have publicly self-identified), officials (prosecutors, judges,
        investigators), institutions (banks, properties, foundations), and
        an &ldquo;other&rdquo; bucket for journalists, witnesses, and
        miscellaneous figures. The search bar matches names, connections,
        and overviews.
    </p>

    <h2>// Technical notes</h2>
    <p>
        The site is a small PHP application backed by MySQL/MariaDB,
        designed to run under XAMPP. The schema and seed entries are in
        the accompanying SQL dump.
    </p>
</div>
