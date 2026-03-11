<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="google" content="notranslate">

<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png">

<link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/header.css'); ?>">
<link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/index.css'); ?>">
<link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/menu.css'); ?>">

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/menu.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/timer.js"></script>

<style>

/* hide google translate UI */
.goog-te-banner-frame,
.goog-te-gadget,
.goog-tooltip,
#goog-gt-tt,
.skiptranslate{
display:none !important;
}

body{
top:0 !important;
}

/* prevent translate for elements with class */
.notranslate{
translate:no;
}

</style>

<?php wp_head(); ?>
<title><?php wp_title(); ?></title>

</head>

<body <?php body_class(); ?>>
    <!-- hidden translate element -->
    <div id="google_translate_element" style="display:none"></div>

    <header id="header">
        <img class="header-logo notranslate" src="<?php echo get_template_directory_uri(); ?>/assets/images/Logo.png" />
        
        <div class="header-buttons">
            <button class="menu-button notranslate" data-button="menu" data-nl="Menu" data-en="Menu">Menu</button>
            <button id="language-picker" class="menu-button notranslate">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/Globe.png" />
                <span id="lang-text">NL</span>
            </button>
        </div>

        <div class="countdown-timer flex">
            <div class="crafted-text notranslate">CRAFTED</div>
            <div class="timer <?php echo (is_front_page() || is_page_template('page-home.php')) ? 'home-page' : 'timer-invisible'; ?>">
                <div class="timerelement">
                    <div id="days" class="timercounter">162</div>
                    <div class="timer-label notranslate" data-nl="dagen" data-en="days">dagen</div>
                </div>
                <div class="divider">:</div>
                <div class="timerelement">
                    <div id="hours" class="timercounter">07</div>
                    <div class="timer-label notranslate" data-nl="uren" data-en="hours">uren</div>
                </div>
                <div class="divider">:</div>
                <div class="timerelement">
                    <div id="minutes" class="timercounter">14</div>
                    <div class="timer-label notranslate" data-nl="minuten" data-en="minutes">minuten</div>
                </div>
                <div class="divider">:</div>
                <div class="timerelement">
                    <div id="seconds" class="timercounter">15</div>
                    <div class="timer-label notranslate" data-nl="seconden" data-en="seconds">seconden</div>
                </div>
            </div>
        </div>
    </header>
    <?php include get_template_directory() . '/menu.php'; ?>

    <script>
        /* google translate init */
        function googleTranslateElementInit(){
            new google.translate.TranslateElement({
                pageLanguage:'nl',
                includedLanguages:'nl,en',
                autoDisplay:false
            },'google_translate_element');
        }

        /* remove google banner if it appears */
        setInterval(function(){
            const banner=document.querySelector('.goog-te-banner-frame');
            if(banner){
                banner.remove();
            }
        },500);

        /* language switch button */
        document.addEventListener("DOMContentLoaded",function(){
            const button=document.getElementById("language-picker");
            const label=document.getElementById("lang-text");

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

        /* keep language after reload */
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
    </script>

    <script>
        function updateManualTranslations(lang){
            document.querySelectorAll('[data-nl][data-en]').forEach(function(el){
                el.textContent=el.getAttribute('data-'+lang);
            });
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>
</html>
