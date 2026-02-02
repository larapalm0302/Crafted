<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRAFTED - Voor studenten, door studenten</title>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/landings-pagina.css">
</head>

<body>
  <!-- Header Navigation -->

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1 class="site-title">CRAFTED</h1>
      <p>Voor studenten, door studenten</p>
    </div>

    <div class="info-bar">
      <span>18 juni</span>
      <span>Klokgebouw Eindhoven</span>
      <span>Gratis</span>
    </div>

    <button class="home-button">
      <a href="http://localhost/wordpress/?page_id=16">↓</a>
    </button>
  </section>

  <script>
    // Menu toggle
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      this.classList.toggle('active');
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth'
          });
        }
      });
    });
  </script>
</body>

</html>