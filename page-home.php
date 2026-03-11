<?php
/*
Template Name: Home Page
*/
get_header();

// Fetch Admin Settings
$video_url = get_option('crafted_home_video_url');
$image_id = get_option('crafted_home_image_id');
$bg_image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : get_template_directory_uri() . '/assets/images/bg-placeholder.jpg';

// Fetch Carousel Texts (Defaults if empty)
$text1 = get_option('crafted_home_carousel_1') ?: '18 juni';
$text2 = get_option('crafted_home_carousel_2') ?: 'Klokgebouw Eindhoven';
$text3 = get_option('crafted_home_carousel_3') ?: 'Gratis Entree';
?>

<!-- ==============================================
     SPLASH SCREEN / HERO SECTION (100vh)
     ============================================== -->
<section class="crafted-hero-section hero">
    <!-- Dynamic Background -->
    <div class="hero-bg-media">
        <?php if (!empty($video_url)): ?>
            <video src="<?= esc_url($video_url) ?>" autoplay loop muted playsinline></video>
        <?php else: ?>
            <div class="hero-bg-image" style="background-image: url('<?= esc_url($bg_image_url) ?>');"></div>
        <?php endif; ?>
        <div class="hero-bg-overlay"></div>
    </div>

    <!-- Hero Content (Matches original front-page.php structure) -->
    <div class="hero-content">
        <h1 class="site-title">CRAFTED</h1>
        <p>Voor studenten, door studenten</p>
    </div>

    <!-- Dynamic Info Bar -->
    <div class="info-bar info-bar-carousel">
        <div class="carousel-track">
            <!-- First Set -->
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
            <!-- Set 2 -->
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
            <!-- Set 3 -->
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
            <!-- Set 4 -->
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
            <!-- Set 5 -->
            <span class="carousel-item"><?= esc_html($text1) ?></span>
            <span class="carousel-item"><?= esc_html($text2) ?></span>
            <span class="carousel-item"><?= esc_html($text3) ?></span>
        </div>
    </div>

    <!-- Down Arrow Button -->
    <button class="home-button hero-scroll-down" aria-label="Scroll naar beneden">
        <svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
    </button>
</section>

<!-- ==============================================
     MAIN CONTENT (Scrolled Target)
     ============================================== -->
<main id="main-content">
   <div class="div1">
    <p class="notranslate">What Awaits You</p>
    <div class="div2">
    </div>
   </div>

   <!-- What Awaits You Cards Section -->
   <section class="awaits-section">
     <div class="cards-container">
       <div class="card">
         <div class="card-image" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/landing.jpg'); ?>');"></div>
         <h3 class="card-title">Programma</h3>
         <p class="card-description">State-of-the-art lighting, visuals, and production design create a multi-sensory journey like no other</p>
         <button class="card-button">→</button>
       </div>

       <div class="card">
         <div class="card-image" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/landing.jpg'); ?>');"></div>
         <h3 class="card-title">Tickets</h3>
         <p class="card-description">World-class artists and emerging talents come together on multiple stages to create unforgettable moments</p>
         <button class="card-button">→</button>
       </div>

       <div class="card">
         <div class="card-image" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/landing.jpg'); ?>');"></div>
         <h3 class="card-title">Crafted & Friends</h3>
         <p class="card-description">Dance until sunrise with cutting-edge electronic music from renowned DJs and producers from around the globe</p>
         <button class="card-button">→</button>
       </div>
     </div>
   </section>

   <!-- Nieuws Section -->
   <section class="nieuws-section">
     <div class="nieuws-container">
       <div class="nieuws-content">
         <h2 class="nieuws-title">Volg hier het laatste nieuws!</h2>
         <p class="nieuws-description">Blijf op de hoogte met teasers, previews en andere interessante updates.</p>
         <button class="nieuws-button">
           <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
             <path d="M3 3h14v14H3V3z" stroke="currentColor" stroke-width="2" fill="none"/>
             <path d="M7 7h6M7 10h6M7 13h4" stroke="currentColor" stroke-width="1.5"/>
           </svg>
           Nieuws
         </button>
       </div>
       <div class="nieuws-image-wrapper">
         <div class="nieuws-image-border">
           <div class="nieuws-image" style="background-image: url('https://via.placeholder.com/400x300?text=Nieuws+Foto');"></div>
         </div>
         <p class="nieuws-credit">Door Summa Marketing</p>
       </div>
     </div>
   </section>

   <!-- Locatie & In de buurt Section -->
   <section class="locatie-section">
     <div class="locatie-container">
       <div class="locatie-column">
         <div class="section-heading">
           <h2>Locatie</h2>
           <span class="heading-underline"></span>
         </div>

         <div class="map-card">
           <div class="map-embed">
             <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2250.1712848719576!2d5.454405376123775!3d51.44860461499746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c6d96b3a220f6b%3A0x3a0eb3741c513904!2sKlokgebouw%2C%20Eindhoven!5e1!3m2!1snl!2snl!4v1769778877889!5m2!1snl!2snl" width="600" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
           </div>
         </div>

         <div class="address-card">
           <div class="address-header">
             <span class="address-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg></span>
             <div>
               <p class="address-title">Adres</p>
               <p class="address-text">Klokgebouw 50<br>5617 AB Eindhoven</p>
             </div>
           </div>
           <a class="route-button" href="#">Routebeschrijving</a>
         </div>
       </div>

       <div class="buurt-column">
         <div class="section-heading">
           <h2>In de buurt</h2>
           <span class="heading-underline"></span>
         </div>

         <div class="buurt-grid">
           <div class="buurt-card">
             <div class="buurt-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M21 5V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v1c0 3.75 2.44 6.94 5.92 8.35L6 20H4v2h16v-2h-2l-2.92-6.65C18.56 11.94 21 8.75 21 5zM8 4h8v1c0 2.21-1.79 4-4 4s-4-1.79-4-4V4z"/></svg></div>
             <div>
               <p class="buurt-title">Biergarten Eindhoven</p>
               <p class="buurt-sub">Bar & terras</p>
               <p class="buurt-distance">300m</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M21 5V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v1c0 3.75 2.44 6.94 5.92 8.35L6 20H4v2h16v-2h-2l-2.92-6.65C18.56 11.94 21 8.75 21 5zM8 4h8v1c0 2.21-1.79 4-4 4s-4-1.79-4-4V4z"/></svg></div>
             <div>
               <p class="buurt-title">Ketelhuis Strijp-S</p>
               <p class="buurt-sub">Bar</p>
               <p class="buurt-distance">300m</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg></div>
             <div>
               <p class="buurt-title">STR’EAT Bars & kitchens</p>
               <p class="buurt-sub">Restaurant</p>
               <p class="buurt-distance">300m</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/></svg></div>
             <div>
               <p class="buurt-title">Hotel Crown</p>
               <p class="buurt-sub">Comfortabel overnachten</p>
               <p class="buurt-distance">800m</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-3.58-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-7H6V6h5v4zm4 0h-5V6h5v4zm1.5 7c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg></div>
             <div>
               <p class="buurt-title">NS Station Eindhoven</p>
               <p class="buurt-sub">Trein & bus verbindingen</p>
               <p class="buurt-distance">1.2km</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M13.2 11H10V7h3.2c1.1 0 2 .9 2 2s-.9 2-2 2zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.2 11H10v4H8V5h5.2c2.21 0 4 1.79 4 4s-1.79 4-4 4z"/></svg></div>
             <div>
               <p class="buurt-title">Parkeergarage P1</p>
               <p class="buurt-sub">24/7 beschikbaar</p>
               <p class="buurt-distance">150m</p>
             </div>
           </div>
         </div>

         <div class="contact-panel">
           <div class="contact-text">
             <h3>Heeft u vragen of andere opmerking?</h3>
             <p>Kom in contact met ons! We helpen u graag verder.</p>
           </div>
           <div class="contact-actions">
             <a class="contact-button" href="mailto:info@example.com">E-mail ons</a>
             <a class="contact-button" href="tel:+31000000000">Bel ons</a>
             <a class="contact-button" href="#">Formulier</a>
           </div>
         </div>
       </div>
     </div>
   </section>
</main>

<!-- JavaScript voor Smooth Scrolling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollBtn = document.querySelector('.hero-scroll-down');
    const target = document.getElementById('main-content');
    
    if(scrollBtn && target) {
        scrollBtn.addEventListener('click', function(e) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        });
    }

    // Controleer of we voorbij de hero sectie zijn gescrolld (100vh)
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