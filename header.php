<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png">
    <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/header.css'); ?>">
    <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/index.css'); ?>">
    <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/menu.css'); ?>">
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/menu.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/timer.js"></script>
    <?php wp_head(); ?>
    <title><?php wp_title(); ?></title>
</head>

<body <?php body_class(); ?>>
    <header id="header">
        <img class="header-logo" src="<?php echo get_template_directory_uri(); ?>/assets/images/Logo.png" />
        <!-- <div class="crafted-text">CRAFTED</div> -->
        <div class="header-buttons">
            <button class="menu-button" data-button="menu">Menu</button>
            <button id="language-picker" class="menu-button" data-button="language">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/Globe.png" />
                NL
            </button>
        </div>
        <div class="countdown-timer flex">
            <div class="crafted-text">CRAFTED</div>
            <div class="timer <?php echo is_front_page() ? 'home-page' : 'timer-invisible'; ?>">
                <div class="timerelement">
                    <div id="days" class="timercounter">162</div>
                    <div class="timer-label">dagen</div>
                </div>
                <div class="divider">:</div>
                <div class="timerelement">
                    <div id="hours" class="timercounter">07</div>
                    <div class="timer-label">uren</div>
                </div>
                <div class="divider">:</div>
                <div class="timerelement">
                    <div id="minutes" class="timercounter">14</div>
                    <div class="timer-label">minuten</div>
                </div>
                <div class="divider">:</div>
                <div class="timerelement">
                    <div id="seconds" class="timercounter">15</div>
                    <div class="timer-label">seconden</div>
                </div>
            </div>
        </div>
    </header>
    <?php include get_template_directory() . '/menu.php'; ?>