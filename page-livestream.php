<?php
/*
Template Name: Livestream
*/
get_header();

// 1. Fetch data from the newest 'livestream' item in the dashboard
$ls_query = new WP_Query(['post_type' => 'livestream', 'posts_per_page' => 1]);

if ($ls_query->have_posts()) {
    $ls_query->the_post();
    $ls_id = get_the_ID();
    $video_url = get_post_meta($ls_id, 'ls_video_url', true) ?: 'https://www.youtube.com/watch?v=w1LIcsG1Wls';
    $channel_name = get_post_meta($ls_id, 'ls_channel_name', true) ?: 'CRAFTED YOUTUBE';
    $logo_id = get_post_meta($ls_id, 'ls_logo_url_id', true);
    $back_url = get_post_meta($ls_id, 'ls_back_url', true);
    wp_reset_postdata();
} else {
    // Fallback to Page Meta (old way)
    $page_id = get_the_ID();
    $video_url = get_post_meta($page_id, 'ls_video_url', true) ?: 'https://www.youtube.com/watch?v=w1LIcsG1Wls';
    $channel_name = get_post_meta($page_id, 'ls_channel_name', true) ?: 'CRAFTED YOUTUBE';
    $logo_id = get_post_meta($page_id, 'ls_logo_url_id', true);
    $back_url = get_post_meta($page_id, 'ls_back_url', true);
}

// Logo URL Resolve
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'thumbnail') : '';
$title = get_the_title();

// Default Back URL
if (empty($back_url) || $back_url === '/') {
    $back_url = home_url();
}

// Extract YouTube ID
$video_id = '';
if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=|live/)|youtu\.be/)([^"&?/\s]{11})%i', $video_url, $matches)) {
    $video_id = $matches[1];
}
?>

<!-- New Structure: Wrapper -> Title (White Area) -> Gradient Area -> Card -->
<style>
    body { overflow-x: hidden !important; }
</style>
<div class="ls-page-wrapper">

    <!-- Title in White Space -->
    <div class="ls-title-area">
        <h1 class="ls-main-title">Kijk mee</h1>
    </div>

    <?php
    // Check if live status is toggled in dashboard
    $is_live = get_post_meta($ls_id, 'ls_is_live', true) === '1';
    ?>

    <!-- Ambient Glow Effect for "Wow" Factor -->
    <div class="ls-ambient-glow"></div>

    <!-- Cinematic Wide Video Section -->
    <div class="ls-cinematic-section">
        
        <?php if (!$is_live): ?>
            <div class="ls-preview-notice">
                Deze livestream is momenteel niet live. Geniet alvast van de on-demand video.
            </div>
        <?php endif; ?>

        <!-- Video Container (Full Width) -->
        <div class="crafted-ls-video-container cinematic-player">
            <?php if ($video_id): ?>
                <iframe src="https://www.youtube.com/embed/<?= esc_attr($video_id) ?>?autoplay=1&mute=1" title="<?= esc_attr($title) ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
            <?php else: ?>
                <div class="crafted-ls-placeholder">
                    <p>Geen geldige video gevonden.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Glassmorphism Overlay Info Panel -->
        <div class="ls-glass-panel">
            <!-- Header with Back Button and LIVE badge -->
            <div class="crafted-ls-header">
                <a href="<?= esc_url($back_url) ?>" class="crafted-ls-back-btn" aria-label="Terug naar overzicht">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Terug
                </a>

                <?php if ($is_live): ?>
                    <div class="crafted-ls-indicator-wrap live-pulsing">
                        <div class="crafted-ls-badge">
                            <span class="ls-icon"></span>
                            <span class="ls-text">LIVE</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="crafted-ls-info">
                <?php if ($logo_url): ?>
                    <div class="crafted-ls-logo">
                        <img src="<?= esc_url($logo_url) ?>" alt="Channel Logo">
                    </div>
                <?php else: ?>
                    <div class="crafted-ls-logo placeholder-logo">C</div>
                <?php endif; ?>

                <div class="crafted-ls-text">
                    <h3 class="crafted-ls-title"><?= wp_kses_post($title) ?></h3>
                    <p class="crafted-ls-channel"><?= wp_kses_post($channel_name) ?></p>
                </div>
            </div>
        </div> <!-- End Glass Panel -->

    </div> <!-- End Cinematic Section -->

</div> <!-- End Wrapper -->

<?php get_footer(); ?>