<?php
/*
Template Name: Home Page
*/
get_header();

$video_url = get_option('crafted_home_video_url');
$image_id = get_option('crafted_home_image_id');
$bg_image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : get_template_directory_uri() . '/assets/images/bg-placeholder.jpg';

$text1 = get_option('crafted_home_carousel_1') ?: '18 juni';
$text2 = get_option('crafted_home_carousel_2') ?: 'Klokgebouw Eindhoven';
$text3 = get_option('crafted_home_carousel_3') ?: 'Gratis Entree';
?>

<section class="crafted-hero-section hero">
    <div class="hero-bg-media">
        <?php if (!empty($video_url)): ?>
        <video src="<?= esc_url($video_url) ?>" autoplay loop muted playsinline></video>
        <?php else: ?>
        <div class="hero-bg-image" style="background-image: url('<?= esc_url($bg_image_url) ?>');"></div>
        <?php endif; ?>
        <div class="hero-bg-overlay"></div>
    </div>

    <div class="hero-content">
        <h1 class="site-title notranslate">CRAFTED</h1>
        <p>Voor studenten, door studenten</p>
    </div>

    <div class="info-bar info-bar-carousel">
        <div class="carousel-track">
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
        </div>
    </div>

    <button class="home-button hero-scroll-down" aria-label="Scroll naar beneden">
        <svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z" />
        </svg>
    </button>
</section>

<main id="main-content">
    <div class="div1">
        <p class="notranslate">What Awaits You</p>
        <div class="div2">
        </div>
    </div>

    <section class="awaits-section">
        <div class="cards-container">
            <?php
       $card_defaults = [
           1 => ['title' => 'Programma', 'desc' => 'State-of-the-art lighting, visuals, and production design create a multi-sensory journey like no other'],
           2 => ['title' => 'Tickets', 'desc' => 'World-class artists and emerging talents come together on multiple stages to create unforgettable moments'],
           3 => ['title' => 'Crafted & Friends', 'desc' => 'Dance until sunrise with cutting-edge electronic music from renowned DJs and producers from around the globe'],
       ];
       $fallback_img = get_template_directory_uri() . '/assets/images/landing.jpg';

       for ($i = 1; $i <= 3; $i++):
           $card_title = get_option("crafted_home_card_{$i}_title", $card_defaults[$i]['title']);
           $card_desc = get_option("crafted_home_card_{$i}_desc", $card_defaults[$i]['desc']);
           $card_image_id = get_option("crafted_home_card_{$i}_image");
           $card_image_url = $card_image_id ? wp_get_attachment_image_url($card_image_id, 'large') : $fallback_img;
           $card_link = get_option("crafted_home_card_{$i}_link", '#');
       ?>
            <div class="card">
                <div class="card-image" style="background-image: url('<?= esc_url($card_image_url) ?>');"></div>
                <h3 class="card-title"><?= esc_html($card_title) ?></h3>
                <p class="card-description"><?= esc_html($card_desc) ?></p>
                <button class="card-button" onclick="window.location.href='<?= esc_url($card_link) ?>'">&#8594;</button>
            </div>
            <?php endfor; ?>
        </div>
    </section>

    <section class="nieuws-section">
        <div class="nieuws-container">
            <div class="nieuws-content">
                <?php
         $nieuws_title = get_option('crafted_home_nieuws_title', 'Volg hier het laatste nieuws!');
         $nieuws_desc = get_option('crafted_home_nieuws_desc', 'Blijf op de hoogte met teasers, previews en andere interessante updates.');
         $nieuws_link = get_option('crafted_home_nieuws_link', '/nieuws');
         ?>
                <h2 class="nieuws-title"><?= esc_html($nieuws_title) ?></h2>
                <p class="nieuws-description"><?= esc_html($nieuws_desc) ?></p>
                <a href="<?= esc_url($nieuws_link) ?>" class="nieuws-button">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3h14v14H3V3z" stroke="currentColor" stroke-width="2" fill="none" />
                        <path d="M7 7h6M7 10h6M7 13h4" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    Nieuws
                </a>
            </div>
            <div class="nieuws-image-wrapper">
                <div class="nieuws-image-border">
                    <?php
           $nieuws_image_id = get_option('crafted_home_nieuws_image');
           $nieuws_image_url = $nieuws_image_id ? wp_get_attachment_image_url($nieuws_image_id, 'large') : 'https://via.placeholder.com/400x300?text=Nieuws+Foto';
           ?>
                    <div class="nieuws-image" style="background-image: url('<?= esc_url($nieuws_image_url) ?>');"></div>
                </div>
                <?php $nieuws_credit = get_option('crafted_home_nieuws_credit', 'Door Summa Marketing'); ?>
                <p class="nieuws-credit"><?= esc_html($nieuws_credit) ?></p>
            </div>
        </div>
    </section>

    <section class="locatie-section">
        <div class="locatie-container">
            <div class="locatie-column">
                <div class="section-heading">
                    <h2>Locatie</h2>
                    <span class="heading-underline"></span>
                </div>

                <div class="map-card">
                    <div class="map-embed">
                        <?php $maps_url = get_option('crafted_home_locatie_maps_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2250.1712848719576!2d5.454405376123775!3d51.44860461499746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c6d96b3a220f6b%3A0x3a0eb3741c513904!2sKlokgebouw%2C%20Eindhoven!5e1!3m2!1snl!2snl!4v1769778877889!5m2!1snl!2snl'); ?>
                        <iframe src="<?= esc_url($maps_url) ?>" width="600" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                <div class="address-card">
                    <div class="address-header">
                        <span class="address-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                            </svg></span>
                        <div>
                            <p class="address-title">Adres</p>
                            <?php
                $adres1 = get_option('crafted_home_locatie_adres_titel', 'Klokgebouw 50');
                $adres2 = get_option('crafted_home_locatie_adres_tekst', '5617 AB Eindhoven');
                ?>
                            <p class="address-text"><?= esc_html($adres1) ?><br><?= esc_html($adres2) ?></p>
                        </div>
                    </div>
                    <?php $route_url = get_option('crafted_home_locatie_route_url', '#'); ?>
                    <a class="route-button" href="<?= esc_url($route_url) ?>">Routebeschrijving</a>
                </div>
            </div>

            <div class="buurt-column">
                <div class="section-heading">
                    <h2>In de buurt</h2>
                    <span class="heading-underline"></span>
                </div>

                <div class="buurt-grid">
                    <?php
            $buurt_icons = [
                'bar' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M21 5V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v1c0 3.75 2.44 6.94 5.92 8.35L6 20H4v2h16v-2h-2l-2.92-6.65C18.56 11.94 21 8.75 21 5zM8 4h8v1c0 2.21-1.79 4-4 4s-4-1.79-4-4V4z"/></svg>',
                'restaurant' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>',
                'hotel' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/></svg>',
                'station' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-3.58-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-7H6V6h5v4zm4 0h-5V6h5v4zm1.5 7c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>',
                'parkeren' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M13.2 11H10V7h3.2c1.1 0 2 .9 2 2s-.9 2-2 2zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.2 11H10v4H8V5h5.2c2.21 0 4 1.79 4 4s-1.79 4-4 4z"/></svg>',
                'overig' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
            ];
            $buurt_defaults = [
                1 => ['title' => 'Biergarten Eindhoven', 'sub' => 'Bar & terras', 'dist' => '300m', 'icon' => 'bar'],
                2 => ['title' => 'Ketelhuis Strijp-S', 'sub' => 'Bar', 'dist' => '300m', 'icon' => 'bar'],
                3 => ['title' => "STR'EAT Bars & kitchens", 'sub' => 'Restaurant', 'dist' => '300m', 'icon' => 'restaurant'],
                4 => ['title' => 'Hotel Crown', 'sub' => 'Comfortabel overnachten', 'dist' => '800m', 'icon' => 'hotel'],
                5 => ['title' => 'NS Station Eindhoven', 'sub' => 'Trein & bus verbindingen', 'dist' => '1.2km', 'icon' => 'station'],
                6 => ['title' => 'Parkeergarage P1', 'sub' => '24/7 beschikbaar', 'dist' => '150m', 'icon' => 'parkeren'],
                7 => ['title' => '', 'sub' => '', 'dist' => '', 'icon' => 'overig'],
                8 => ['title' => '', 'sub' => '', 'dist' => '', 'icon' => 'overig'],
            ];
            for ($i = 1; $i <= 8; $i++):
                $def = $buurt_defaults[$i];
                $b_title = get_option("crafted_home_buurt_{$i}_title", $def['title']);
                if (empty($b_title)) continue;
                $b_sub = get_option("crafted_home_buurt_{$i}_sub", $def['sub']);
                $b_dist = get_option("crafted_home_buurt_{$i}_dist", $def['dist']);
                $b_icon = get_option("crafted_home_buurt_{$i}_icon", $def['icon']);
                $icon_svg = isset($buurt_icons[$b_icon]) ? $buurt_icons[$b_icon] : $buurt_icons['overig'];
            ?>
                    <div class="buurt-card">
                        <div class="buurt-icon"><?= $icon_svg ?></div>
                        <div>
                            <p class="buurt-title"><?= esc_html($b_title) ?></p>
                            <p class="buurt-sub"><?= esc_html($b_sub) ?></p>
                            <p class="buurt-distance"><?= esc_html($b_dist) ?></p>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="contact-panel">
                    <div class="contact-text">
                        <h3>Heeft u vragen of andere opmerking?</h3>
                        <p>Kom in contact met ons! We helpen u graag verder.</p>
                    </div>
                    <?php
                    $contact_email = get_option('crafted_home_contact_email', '');
                    $contact_telefoon = get_option('crafted_home_contact_telefoon', '');
                    $contact_url = get_option('crafted_home_contact_url', '#');
                    ?>
                    <div class="contact-actions">
                        <?php if ($contact_email): ?>
                        <a class="contact-button" href="mailto:<?= esc_attr($contact_email) ?>">E-mail ons</a>
                        <?php endif; ?>
                        <?php if ($contact_telefoon): ?>
                        <a class="contact-button" href="tel:<?= esc_attr($contact_telefoon) ?>">Bel ons</a>
                        <?php endif; ?>
                        <a class="contact-button" href="<?= esc_url($contact_url) ?>">Formulier</a>
                    </div>
                </div>
            </div>
        </div>

        <?php
    $plat_title = get_option('crafted_home_plattegrond_titel', 'Plattegrond');
    $plat_text  = get_option('crafted_home_plattegrond_tekst', '');
    $plat_img_id = get_option('crafted_home_plattegrond_img');
    $plat_img_src = '';
    if ($plat_img_id) {
        $plat_img_src = wp_get_attachment_image_url($plat_img_id, 'full');
    }
    
    if (!empty($plat_title) || !empty($plat_img_src) || !empty($plat_text)) :
    ?>
        <section class="plattegrond-section">
            <div class="plattegrond-container<?= empty($plat_text) ? ' plattegrond-full' : ' plattegrond-split' ?>">

                <?php if (!empty($plat_text)) : ?>
                <div class="plattegrond-content">
                    <?php if (!empty($plat_title)) : ?>
                    <div class="section-heading" style="text-align:left;">
                        <h2><?= esc_html($plat_title) ?></h2>
                        <span class="heading-underline"></span>
                    </div>
                    <?php endif; ?>
                    <div class="plattegrond-text">
                        <?= wpautop(wp_kses_post($plat_text)) ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="plattegrond-visual">
                    <?php if (empty($plat_text) && !empty($plat_title)) : ?>
                    <div class="section-heading">
                        <h2><?= esc_html($plat_title) ?></h2>
                        <span class="heading-underline"></span>
                    </div>
                    <?php endif; ?>

                    <div class="map-card plattegrond-card">
                        <?php if ($plat_img_src) : ?>
                        <a href="<?= esc_url($plat_img_src) ?>" target="_blank" title="Bekijk grote plattegrond">
                            <img src="<?= esc_url($plat_img_src) ?>" alt="Plattegrond" class="plattegrond-img">
                        </a>
                        <?php else : ?>
                        <div class="plattegrond-placeholder">
                            <p>Geen plattegrond geselecteerd.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </section>
        <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollBtn = document.querySelector('.hero-scroll-down');
    const target = document.getElementById('main-content');

    if (scrollBtn && target) {
        scrollBtn.addEventListener('click', function(e) {
            e.preventDefault();
            target.scrollIntoView({
                behavior: 'smooth'
            });
        });
    }

    function checkHeaderSticky() {
        if (window.scrollY >= window.innerHeight - 5) {
            document.body.classList.add('header-stuck');
        } else {
            document.body.classList.remove('header-stuck');
        }
    }
    window.addEventListener('scroll', checkHeaderSticky);
    window.addEventListener('resize', checkHeaderSticky);
    checkHeaderSticky(); // Voer ook één keer uit bij laden
});
</script>

<?php get_footer(); ?>