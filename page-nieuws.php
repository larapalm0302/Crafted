<?php 
wp_enqueue_style('page-contact-style', get_template_directory_uri() . '/assets/css/nieuws.css');
wp_enqueue_script('nieuws-bericht-script', get_template_directory_uri() . '/assets/js/translate_date.js', array(), false, true);
get_header();
 ?>
<div class="news-page-wrapper">
    <div id="head-text">Het laatste nieuws</div>
    <div id="teasers">
        <div class="teaser-header-text">Teasers</div>
        <div class="teaser-container">
            <?php
                $teasers_query = new WP_Query([
                    'post_type' => 'teasers',
                    'posts_per_page' => 8,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ]);
                if ($teasers_query->have_posts()):
                    while ($teasers_query->have_posts()): $teasers_query->the_post();
                        $description = get_post_meta(get_the_ID(), 'description', true);
                        $image_id = get_post_meta(get_the_ID(), 'teasers_image', true);
                        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
                        $video_url = get_post_meta(get_the_ID(), 'teasers_video', true);
                ?>
            <a class="teaser-item" href="<?php echo esc_url(get_permalink()); ?>">
                <div class="img-container" style="position: relative;">
                    <div class="border"></div>
                    <?php if ($image_url): ?>
                        <img class="img" src="<?php echo esc_url($image_url); ?>" alt="Teaser afbeelding" />
                    <?php else: ?>
                        <div class="img" style="background: #222; display: flex; align-items: center; justify-content: center; height: 100%; width: 100%; border-radius: 8px;"></div>
                    <?php endif; ?>
                    <?php if (!empty($video_url)): ?>
                        <div class="video-play-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.35); z-index: 2; border-radius: 8px;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" fill="rgba(119, 53, 124, 0.9)" stroke="#fff" stroke-width="2"/>
                                <path d="M10 8L16 12L10 16V8Z" fill="#fff"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="teaser-description"><?php echo esc_html($description); ?></div>
            </a>
            <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
            <p style="font-size:24px;color:#77357c;text-align:center;">Geen nieuws gevonden.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="news-columns-container">
        <div id="news-container">
            <?php
                    $news_query = new WP_Query([
                        'post_type' => 'nieuws',
                        'posts_per_page' => 10,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ]);
                    if ($news_query->have_posts()):
                        while ($news_query->have_posts()): $news_query->the_post();
                            $description = get_post_meta(get_the_ID(), 'description', true);
                            $image_id = get_post_meta(get_the_ID(), 'nieuws_image', true);
                            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
                            $datetime = get_post_meta($post->ID, 'nieuws_date', true);
                            if (empty($datetime)) {
                                $datetime = date_i18n('l j F, H:i');
                            }
                            $topic = get_post_meta(get_the_ID(), 'nieuws_topic', true);
                    ?>
            <a class="news-item" href="<?php echo esc_url(get_permalink()); ?>">
                <div class="news-img-border"></div>
                <img class="news-img" src="<?php echo esc_url($image_url); ?>" alt="Nieuws afbeelding" />
                <div class="news-info">
                    <div class="news-time" data-datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html($datetime); ?></div>
                    <div class="news-title"><?php the_title(); ?></div>
                    <p class="news-descriptions"><?php echo esc_html($description); ?></p>
                </div>
            </a>
            <?php
                        endwhile;
                        wp_reset_postdata();
                    else:
                    ?>
            <p style="font-size:24px;color:#77357c;text-align:center;">Geen nieuws gevonden.</p>
            <?php endif; ?>
        </div>

        <div id="storylines">
            <div class="storylines-header-text">Storylines</div>
            <div class="storylines-container">
                <?php
                    $storylines_query = new WP_Query([
                        'post_type' => 'storylines',
                        'posts_per_page' => 10,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ]);
                    if ($storylines_query->have_posts()):
                        while ($storylines_query->have_posts()): $storylines_query->the_post();
                            $description = get_post_meta(get_the_ID(), 'description', true);
                            $image_id = get_post_meta(get_the_ID(), 'storylines_image', true);
                            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        
                    ?>
                <a class="storylines-item" href="<?php echo esc_url(get_permalink()); ?>">
                    <div class="img-container">
                        <div class="border"></div>
                        <img class="img" src="<?php echo esc_url($image_url); ?>" alt="Storyline afbeelding" />
                    </div>
                    <p class="title"><?php echo esc_html($description); ?></p>
                </a>
                <?php
                        endwhile;
                        wp_reset_postdata();
                    else:
                    ?>
                <p style="font-size:24px;color:#77357c;text-align:center;">Geen nieuws gevonden.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>