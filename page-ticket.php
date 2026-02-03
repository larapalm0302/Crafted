<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summa Fashion Show</title>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/ticket.css">
</head>
<body>
    <div class="container">
        <div class="content-wrapper">
            <div class="left-section">
                <h1>Summa fashion show</h1>
                
                <p>Welkom bij de jaarlijkse CRAFTED Fashion Show! Een spetterend evenement waar creativiteit, mode en talent samenkomen. Onze getalenteerde studenten presenteren hun unieke ontwerpen op de catwalk.</p>
                
                <p>Verwacht een avond vol verrassende creaties, van avant-garde tot streetwear. Elke collectie vertelt een verhaal en laat de passie en vakmanschap van onze studenten zien.</p>

                <ul class="features">
                    <li class="feature-item">
                        <div class="feature-icon">🎭</div>
                        <div class="feature-text">
                            <h3>Professionele catwalkshow</h3>
                            <p>Alle musea, live en entertainment</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon">✨</div>
                        <div class="feature-text">
                            <h3>Unieke studentenwerpen</h3>
                            <p>Ontdek exclusieve creaties van onze studenten</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon">🎉</div>
                        <div class="feature-text">
                            <h3>Gratis toegang!</h3>
                            <p>Reserveer nu uw plaats vol tal nu bij</p>
                        </div>
                    </li>
                </ul>

                <a href="https://www.tickettailor.com/events/summacollege" class="cta-button">
                    Reserveer nu je gratis tickets!
                    <span class="button-subtitle">Klik voor de ticket pagina</span>
                </a>

                <div class="footer-link">
                    <a href="https://www.tickettailor.com/events/summacollege">https://www.tickettailor.com/events/summacollege</a>
                </div>
            </div>

            <div class="right-section">
                <div class="main-image">
                    <img src="path/to/main-fashion-image.jpg" alt="Catwalk show">
                </div>
                <div class="secondary-image">
                    <img src="path/to/image-1.jpg" alt="Fashion detail 1">
                </div>
                <div class="secondary-image">
                    <img src="path/to/image-2.jpg" alt="Fashion detail 2">
                </div>
            </div>
        </div>
    </div>

    

    <?php get_footer(); ?>