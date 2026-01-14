<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/header.css' ); ?>">
    <?php wp_head(); ?>
    <title><?php wp_title(); ?></title>
</head>
<body <?php body_class(); ?>>
    <header>Ik ben de header</header>