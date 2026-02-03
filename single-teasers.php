<?php 
wp_enqueue_style('nieuws-bericht-style', get_template_directory_uri() . '/assets/css/nieuws-bericht.css');
wp_enqueue_script('nieuws-bericht-script', get_template_directory_uri() . '/assets/js/translate_date.js', array(), false, true);
get_header();
?>

<?php while (have_posts()) : the_post(); 
    $description = get_post_meta(get_the_ID(), 'description', true);
    $image_id = get_post_meta(get_the_ID(), 'teasers_image', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
    $datetime = get_post_meta(get_the_ID(), 'teasers_date', true);
    if (empty($datetime)) {
        $datetime = date_i18n('l j F, H:i');
    }
?>

<main>
    <article>
        <?php if ($image_url): ?>
        <div class="news-image-wrapper">
            <img class="news-img" src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>">
        </div>
        <?php endif; ?>

        <div class="news-date" data-datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html($datetime); ?></div>

        <h1 class="news-title"><?php the_title(); ?></h1>

        <div class="news-descriptions">
            <?php echo wp_kses_post(wpautop($description)); ?>
        </div>
    </article>
</main>

<?php endwhile; ?>
<?php get_footer(); ?>