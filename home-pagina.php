<?php
/*
Template Name: Home Page
*/
get_header(); 
?>

<main>
   <div class="div1">
    <p>What Awaits You</p>
    <div class="div2">
    </div>
   </div>

   <!-- What Awaits You Cards Section -->
   <section class="awaits-section">
     <div class="cards-container">
       <div class="card">
         <div class="card-image" style="background-image: url('https://via.placeholder.com/300x250?text=Programma');"></div>
         <h3 class="card-title">Programma</h3>
         <p class="card-description">State-of-the-art lighting, visuals, and production design create a multi-sensory journey like no other</p>
         <button class="card-button">→</button>
       </div>

       <div class="card">
         <div class="card-image" style="background-image: url('https://via.placeholder.com/300x250?text=Tickets');"></div>
         <h3 class="card-title">Tickets</h3>
         <p class="card-description">World-class artists and emerging talents come together on multiple stages to create unforgettable moments</p>
         <button class="card-button">→</button>
       </div>

       <div class="card">
         <div class="card-image" style="background-image: url('https://via.placeholder.com/300x250?text=Crafted%20%26%20Friends');"></div>
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
             <span class="address-icon">📍</span>
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
             <div class="buurt-icon">🍴</div>
             <div>
               <p class="buurt-title">Biergarten Eindhoven</p>
               <p class="buurt-sub">Bar & terras</p>
               <p class="buurt-distance">300m</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon">🍹</div>
             <div>
               <p class="buurt-title">Ketelhuis Strijp-S</p>
               <p class="buurt-sub">Bar</p>
               <p class="buurt-distance">300m</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon">🍽️</div>
             <div>
               <p class="buurt-title">STR’EAT Bars & kitchens</p>
               <p class="buurt-sub">Restaurant</p>
               <p class="buurt-distance">300m</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon">🏨</div>
             <div>
               <p class="buurt-title">Hotel Crown</p>
               <p class="buurt-sub">Comfortabel overnachten</p>
               <p class="buurt-distance">800m</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon">🚆</div>
             <div>
               <p class="buurt-title">NS Station Eindhoven</p>
               <p class="buurt-sub">Trein & bus verbindingen</p>
               <p class="buurt-distance">1.2km</p>
             </div>
           </div>
           <div class="buurt-card">
             <div class="buurt-icon">🅿️</div>
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

<?php get_footer(); ?>