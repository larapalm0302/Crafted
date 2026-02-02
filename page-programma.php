<?php
/*
Template Name: Programma
*/
wp_enqueue_style('page-programma-style', get_template_directory_uri() . '/assets/page-programma.css');

get_header();

$query = new WP_Query([
    'post_type' => 'programma',
    'meta_key' => 'start_time',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'posts_per_page' => -1
]);

$grouped_items = [];
while ($query->have_posts()) {
    $query->the_post();
    $start_time = get_field('start_time') ?: '00:00';
    $end_time = get_field('end_time') ?: '00:00';
    $time_key = $start_time;
    
    $grouped_items[$time_key][] = [
        'title' => get_the_title(),
        'description' => get_field('description') ?: get_the_excerpt(),
        'image_id' => get_post_meta(get_the_ID(), 'programma_image', true),
        'start_time' => $start_time,
        'end_time' => $end_time,
    ];
}
wp_reset_postdata();
?>

<main class="program-page">
    <h1 class="program-title">Programma crafted 2026</h1>

    <?php foreach ($grouped_items as $time_slot => $items): ?>
    <div class="day-section">
        <div class="day-header">
            <span class="chevron">&#8964;</span>
            <span class="day-date"><?= esc_html($time_slot) ?></span>
            <span class="chevron">&#8964;</span>
        </div>

        <div class="day-events">
            <?php foreach ($items as $item): 
                $image_url = $item['image_id'] ? wp_get_attachment_image_url($item['image_id'], 'medium') : '';
            ?>
            <div class="program-card">
                <div class="card-image">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                    <?php else: ?>
                        <div class="image-placeholder">[image PH]</div>
                    <?php endif; ?>
                </div>

                <div class="card-content">
                    <div class="card-time"><?= esc_html($item['start_time']) ?> - <?= esc_html($item['end_time']) ?></div>
                    <h3 class="card-title"><?= esc_html($item['title']) ?></h3>
                    <p class="card-description"><?= esc_html($item['description']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    
</main>

<?php get_footer(); ?>

