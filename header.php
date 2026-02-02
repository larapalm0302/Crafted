<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/header.css'); ?>">
    <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/front-page.css'); ?>">
    <?php wp_head(); ?>
    <title><?php wp_title(); ?></title>
</head>

<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="header-inner">

            <div class="header-left">
                <img class="logo" src="<?php echo esc_url( get_template_directory_uri() . '/images/Image%20(Logo).png' ); ?>" alt="Logo">
            </div>

            <!-- Midden: Titel + countdown -->
            <div class="header-center">
                <h1 class="site-title">CRAFTED</h1>

                <div class="countdown">
                    <div class="time-box">
                        <span id="days">00</span>
                        <small>DAGEN</small>
                    </div>
                    <span class="colon">:</span>
                    <div class="time-box">
                        <span id="hours">00</span>
                        <small>UREN</small>
                    </div>
                    <span class="colon">:</span>
                    <div class="time-box">
                        <span id="minutes">00</span>
                        <small>MINUTEN</small>
                    </div>
                    <span class="colon">:</span>
                    <div class="time-box">
                        <span id="seconds">00</span>
                        <small>SECONDEN</small>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <!-- plek voor zoek/knoppen -->
            </div>

        </div>
    </header>