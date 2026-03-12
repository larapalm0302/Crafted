<?php
/*
Template Name: Coming Soon / Countdown Pagina
*/

// Fetch Admin Settings for background
$video_url = get_option('crafted_home_video_url');
$image_id = get_option('crafted_home_image_id');
$bg_image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : get_template_directory_uri() . '/assets/images/bg-placeholder.jpg';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png">
    
    <!-- Using same base styling as home page for the background -->
    <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/index.css'); ?>">
    
    <!-- We inline some specific styles to make the timer massive and central -->
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            font-family: "Roboto", Helvetica, sans-serif;
            background-color: #000;
        }

        .coming-soon-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .coming-soon-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            opacity: 0.8; /* Slightly darker to make text pop */
        }
        
        .coming-soon-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(119, 53, 124, 0.7) 0%, rgba(226, 115, 145, 0.6) 100%);
            z-index: 1;
        }

        .cs-logo {
            width: 150px;
            margin-bottom: 20px;
            border-radius: 50%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            animation: pulse 3s infinite alternate;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 15px rgba(255, 234, 153, 0.4); }
            100% { box-shadow: 0 0 25px rgba(255, 234, 153, 0.8); }
        }

        .cs-title {
            color: #fff;
            font-size: clamp(30px, 6vw, 60px);
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 10px;
            text-shadow: 0 4px 15px rgba(0,0,0,0.5);
            text-align: center;
        }
        
        .cs-subtitle {
            color: #FFEA99;
            font-size: clamp(16px, 3vw, 24px);
            margin-bottom: 50px;
            text-align: center;
            max-width: 80%;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        /* Large Timer specific styles */
        .cs-timer {
            display: flex;
            align-items: center;
            color: #fff;
            font-weight: 900;
            background: rgba(0, 0, 0, 0.4);
            padding: 20px 40px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .cs-timerelement {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 80px;
        }

        .cs-timercounter {
            font-size: clamp(40px, 8vw, 80px);
            line-height: 1;
            text-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }

        .cs-timer-label {
            font-size: clamp(12px, 2vw, 18px);
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
        }

        .cs-divider {
            font-size: clamp(30px, 6vw, 60px);
            margin: 0 15px;
            margin-bottom: 25px;
            color: #FFEA99;
        }
        
        .cs-livestream-btn {
            display: inline-block;
            background: linear-gradient(135deg, #77357c 0%, #e27391 100%);
            color: #fff;
            text-decoration: none;
            font-size: clamp(18px, 3vw, 24px);
            font-weight: 700;
            padding: 15px 40px;
            border-radius: 40px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 25px rgba(119, 53, 124, 0.5);
            transition: all 0.3s ease;
            margin-top: 30px;
        }
        
        .cs-livestream-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 35px rgba(226, 115, 145, 0.6);
        }

        @media (max-width: 600px) {
            .cs-timer {
                padding: 15px 20px;
            }
            .cs-timerelement {
                min-width: 60px;
            }
            .cs-divider {
                margin: 0 8px;
                margin-bottom: 15px;
            }
        }
        
        /* Hide Google Translate Banner completely */
        body { top: 0 !important; }
        .skiptranslate iframe { display: none !important; }

        /* Container to place the menu-button at the top right */
        .cs-lang-container {
            position: absolute;
            top: 30px;
            right: 40px;
            z-index: 1000;
        }

        /* Standalone Base Styling for the button (copied from header.css since it's not loaded here) */
        .cs-lang-container .menu-button {
            width: 100px;
            height: 50px;
            background-color: #ffea99;
            border: none;
            border-radius: 0;
            position: relative;
            outline: none;
            font-size: 20px;
            font-weight: 900;
            font-family: "Roboto", Helvetica, sans-serif;
            line-height: 20px;
            color: #000;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .cs-lang-container .menu-button::before {
            top: 4px;
            left: -9px;
            bottom: -4px;
            width: 9px;
            transform: skewY(-45deg);
            content: "";
            position: absolute;
            background: #77357c;
            transition: all 0.2s ease;
        }

        .cs-lang-container .menu-button::after {
            bottom: -8.9px;
            left: -5px;
            right: 5px;
            height: 9px;
            transform: skewX(-45deg);
            content: "";
            position: absolute;
            background: #77357c;
            transition: all 0.2s ease;
        }

        .cs-lang-container .menu-button:hover {
            translate: -2px 2px;
            background-color: #fff4cc;
        }

        .cs-lang-container .menu-button:hover::before {
            top: 3px; left: -7px; bottom: -3px; width: 7px;
        }

        .cs-lang-container .menu-button:hover::after {
            bottom: -6.9px; left: -4px; right: 4px; height: 7px;
        }

        .cs-lang-container .menu-button:active {
            translate: -4px 4px;
        }

        .cs-lang-container .menu-button:active::before {
            top: 2px; left: -5px; bottom: -2px; width: 5px;
        }

        .cs-lang-container .menu-button:active::after {
            bottom: -4.9px; left: -3px; right: 3px; height: 5px;
        }

        .cs-lang-container .menu-button img {
            width: 24px;
            height: 24px;
        }
        
        @media (max-width: 600px) {
            .cs-lang-container {
                top: 20px;
                right: 20px;
            }
            .cs-lang-container .menu-button {
                width: 80px;
                height: 40px;
                font-size: 16px;
            }
            .cs-lang-container .menu-button img {
                width: 18px;
                height: 18px;
            }
            .cs-lang-container .menu-button::before { width: 7px; left: -7px; top: 3px; bottom: -3px; }
            .cs-lang-container .menu-button::after { height: 7px; bottom: -6.9px; left: -3px; right: 3px; }
        }
    </style>
    
    <?php
    // Expose specific Coming Soon timer settings to JavaScript
    $timer_date = get_option('crafted_cs_timer_date', '2026-06-18T00:00:00');
    $expired_text = get_option('crafted_cs_expired_text', 'Kijk de livestream');
    $expired_url = get_option('crafted_cs_expired_url', '/livestream');
    ?>
    <script>
        window.craftedTimerSettings = {
            targetDate: "<?php echo esc_js($timer_date); ?>",
            expiredText: "<?php echo esc_js($expired_text); ?>",
            expiredUrl: "<?php echo esc_js($expired_url); ?>"
        };
    </script>
    <title>Coming Soon - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <!-- hidden translate element -->
    <div id="google_translate_element" style="display:none"></div>

    <!-- Standalone Language Button -->
    <div class="cs-lang-container">
        <button id="cs-language-picker" class="menu-button notranslate">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/Globe.png" alt="Taal">
            <span id="cs-lang-text">NL</span>
        </button>
    </div>

    <!-- Dynamic Background -->
    <?php if (!empty($video_url)): ?>
        <video class="coming-soon-bg" src="<?= esc_url($video_url) ?>" autoplay loop muted playsinline></video>
    <?php else: ?>
        <img class="coming-soon-bg" src="<?= esc_url($bg_image_url) ?>" alt="Background">
    <?php endif; ?>
    <div class="coming-soon-overlay"></div>

    <!-- Content -->
    <div class="coming-soon-wrapper">
        <img class="cs-logo" src="<?php echo get_template_directory_uri(); ?>/assets/images/Logo.png" alt="Crafted Logo">
        
        <?php
        $title_nl = get_option('crafted_cs_title_nl', 'Snel online');
        $title_en = get_option('crafted_cs_title_en', 'Coming Soon');
        $sub_nl   = get_option('crafted_cs_sub_nl', 'We zijn nog even bezig met bouwen. De website lanceert over:');
        $sub_en   = get_option('crafted_cs_sub_en', 'We are currently building the site. We will launch in:');
        ?>
        <h1 class="cs-title notranslate" data-nl="<?= esc_attr($title_nl) ?>" data-en="<?= esc_attr($title_en) ?>"><?= esc_html($title_nl) ?></h1>
        <p class="cs-subtitle notranslate" data-nl="<?= esc_attr($sub_nl) ?>" data-en="<?= esc_attr($sub_en) ?>"><?= esc_html($sub_nl) ?></p>

        <!-- Big Timer -->
        <div id="cs-countdown-container" class="cs-timer">
            <div class="cs-timerelement">
                <div id="cs-days" class="cs-timercounter">00</div>
                <div class="cs-timer-label">dagen</div>
            </div>
            <div class="cs-divider">:</div>
            <div class="cs-timerelement">
                <div id="cs-hours" class="cs-timercounter">00</div>
                <div class="cs-timer-label">uren</div>
            </div>
            <div class="cs-divider">:</div>
            <div class="cs-timerelement">
                <div id="cs-minutes" class="cs-timercounter">00</div>
                <div class="cs-timer-label">minuten</div>
            </div>
            <div class="cs-divider">:</div>
            <div class="cs-timerelement">
                <div id="cs-seconds" class="cs-timercounter">00</div>
                <div class="cs-timer-label">sec</div>
            </div>
        </div>
    </div>

    <!-- Standalone Timer Script for this page -->
    <script>
        function updateBigCountdown() {
            if (!window.craftedTimerSettings) return;

            const targetDateStr = window.craftedTimerSettings.targetDate || '2026-06-18T00:00:00';
            const targetDate = new Date(targetDateStr).getTime();
            const now = new Date().getTime();
            const distance = targetDate - now;

            const daysEl = document.getElementById('cs-days');
            const hoursEl = document.getElementById('cs-hours');
            const minutesEl = document.getElementById('cs-minutes');
            const secondsEl = document.getElementById('cs-seconds');

            if (!daysEl || !hoursEl || !minutesEl || !secondsEl) return;

            if (distance < 0) {
                const container = document.getElementById('cs-countdown-container');
                if (container) {
                    // Replaced with livestream link, style overriden to center it nicely
                    container.style.background = 'transparent';
                    container.style.border = 'none';
                    container.style.boxShadow = 'none';
                    container.style.backdropFilter = 'none';
                    container.innerHTML = `
                        <a href="${window.craftedTimerSettings.expiredUrl}" class="cs-livestream-btn">
                            ${window.craftedTimerSettings.expiredText}
                        </a>
                    `;
                }
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            daysEl.textContent = String(days).padStart(2, '0');
            hoursEl.textContent = String(hours).padStart(2, '0');
            minutesEl.textContent = String(minutes).padStart(2, '0');
            secondsEl.textContent = String(seconds).padStart(2, '0');
        }

        let bigTimerInterval;
        document.addEventListener('DOMContentLoaded', function () {
            updateBigCountdown();
            if (document.getElementById('cs-days')) {
                bigTimerInterval = setInterval(updateBigCountdown, 1000);
            }
        });
    </script>
    
    <!-- Translation Script Logic -->
    <script>
        function googleTranslateElementInit(){
            new google.translate.TranslateElement({
                pageLanguage:'nl',
                includedLanguages:'nl,en',
                autoDisplay:false
            },'google_translate_element');
        }

        setInterval(function(){
            const banner=document.querySelector('.goog-te-banner-frame');
            if(banner) banner.remove();
        },500);

        document.addEventListener("DOMContentLoaded",function(){
            const button=document.getElementById("cs-language-picker");
            const label=document.getElementById("cs-lang-text");

            if(button) {
                button.addEventListener("click",function(){
                    const select=document.querySelector(".goog-te-combo");
                    if(!select)return;

                    if(select.value==="nl"){
                        select.value="en";
                        if(label) label.innerText="EN";
                        localStorage.setItem("siteLang","en");
                        updateManualTranslations("en");
                    }else{
                        select.value="nl";
                        if(label) label.innerText="NL";
                        localStorage.setItem("siteLang","nl");
                        updateManualTranslations("nl");
                    }
                    select.dispatchEvent(new Event("change"));
                });
            }
        });

        window.addEventListener("load",function(){
            const savedLang=localStorage.getItem("siteLang");
            if(!savedLang)return;

            const interval=setInterval(function(){
                const select=document.querySelector(".goog-te-combo");
                if(select){
                    select.value=savedLang;
                    select.dispatchEvent(new Event("change"));
                    updateManualTranslations(savedLang);
                    clearInterval(interval);
                }
            },300);
        });

        function updateManualTranslations(lang){
            document.querySelectorAll('[data-nl][data-en]').forEach(function(el){
                el.textContent=el.getAttribute('data-'+lang);
            });
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <?php wp_footer(); ?>
</body>
</html>
