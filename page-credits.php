<?php
wp_enqueue_style('credits-style', get_template_directory_uri() . '/assets/css/credits.css');
get_header();
?>

<main class="credits-page">
    <section class="credits-hero">
        <a href="javascript:history.back()" class="footer-credits-link credits-back-btn">&#8592; Terug</a>
        <p class="credits-kicker notranslate">CRAFTED</p>
        <h1>Credits</h1>
        <p class="credits-intro">Dit project is gemaakt door een team studenten met oog voor concept, design en development.</p>
    </section>

    <section class="credits-grid" aria-label="Project credits">
        <article class="credits-card">
            <h2>Developers</h2>
            <ul class="credits-list">
                <li class="notranslate">Nick Verbruggen</li>
                <li class="notranslate">Stan Hoenselaars</li>
                <li class="notranslate">Lara Palm</li>
                <li class="notranslate">Nik Rasa</li>
            </ul>
        </article>

        <article class="credits-card">
            <h2>Tools gebruikt</h2>
            <ul class="credits-list credits-tools">
                <li>WordPress</li>
                <li>VSCode</li>
                <li>Git</li>
                <li>Figma</li>
            </ul>
        </article>
    </section>
</main>