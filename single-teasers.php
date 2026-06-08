<?php 
wp_enqueue_style('nieuws-bericht-style', get_template_directory_uri() . '/assets/css/nieuws-bericht.css');
wp_enqueue_script('nieuws-bericht-script', get_template_directory_uri() . '/assets/js/translate_date.js', array(), false, true);
get_header();
?>

<?php while (have_posts()) : the_post(); 
    $description = get_post_meta(get_the_ID(), 'description', true);
    $image_id = get_post_meta(get_the_ID(), 'teasers_image', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
    $video_url = get_post_meta(get_the_ID(), 'teasers_video', true);
    $datetime = get_post_meta(get_the_ID(), 'teasers_date', true);
    if (empty($datetime)) {
        $datetime = date_i18n('l j F, H:i');
    }

    $video_id = '';
    if (!empty($video_url)) {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=|live/)|youtu\.be/)([^"&?/\s]{11})%i', $video_url, $matches)) {
            $video_id = $matches[1];
        }
    }
?>

<main>
    <article>
        <?php if ($video_id): ?>
        <div class="news-image-wrapper" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; margin-bottom: 20px;">
            <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>" 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; border-radius: 8px;"
                    allowfullscreen>
            </iframe>
        </div>
        <?php elseif (!empty($video_url)): ?>
        <div class="news-image-wrapper" style="margin-bottom: 20px;">
            <video controls src="<?php echo esc_url($video_url); ?>" class="news-img" style="width: 100%; height: auto; border-radius: 8px; max-height: 500px; object-fit: cover;"></video>
        </div>
        <?php elseif ($image_url): ?>
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