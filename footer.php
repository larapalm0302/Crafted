<!-- FOOTER -->
<footer class="crafted-footer">
    <div class="footer-gradient-bg"></div>

    <div class="footer-content-wrapper">

        <!-- TOP SECTION: Quick Links & Socials -->
        <div class="footer-top">

            <!-- LEFT: Quick Links -->
            <div class="footer-links-section">
                <h3>Quick Links</h3>
                <div class="footer-buttons-grid">
                    <?php
                    $icons = ['dashicons-tickets-alt', 'dashicons-groups', 'dashicons-format-audio', 'dashicons-editor-help']; // Fallback icons
                    for ($i = 1; $i <= 4; $i++):
                        $text = get_option("crafted_footer_btn_{$i}_text", "Knop $i");
                        $url = get_option("crafted_footer_btn_{$i}_url", "#");
                        ?>
                    <a href="<?= esc_url($url) ?>" class="footer-glass-btn">
                        <div class="btn-icon-box">
                            <!-- Using Dashicons for simplicity, can be replaced with SVGs -->
                            <span class="dashicons <?= $icons[$i - 1] ?>"></span>
                        </div>
                        <span class="btn-text"><?= esc_html($text) ?></span>
                    </a>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- RIGHT: Socials -->
            <div class="footer-socials-section">
                <h3>Volg ons</h3>
                <p>Deel je ervaring met <strong>#CRAFTED</strong></p>

                <div class="social-icons-row">
                    <?php
                    $socials = [
                        'instagram' => ['url' => get_option('crafted_social_insta'), 'icon' => 'dashicons-instagram'],
                        'linkedin' => ['url' => get_option('crafted_social_linkedin'), 'icon' => 'dashicons-linkedin'],
                        'tiktok' => ['url' => get_option('crafted_social_tiktok'), 'icon' => 'dashicons-video-alt3'], // Dashicons doesn't have tiktok, using video alt
                        'youtube' => ['url' => get_option('crafted_social_youtube'), 'icon' => 'dashicons-youtube'],
                    ];

                    foreach ($socials as $key => $data):
                        if ($data['url']):
                            ?>
                    <a href="<?= esc_url($data['url']) ?>" target="_blank" class="social-circle-btn"
                       aria-label="<?= ucfirst($key) ?>">
                        <span class="dashicons <?= $data['icon'] ?>"></span>
                    </a>
                    <?php endif; endforeach; ?>
                </div>
            </div>

        </div>

        <!-- BOTTOM SECTION: Sponsors Marquee -->
        <div class="footer-sponsors-section">
            <h3 class="sponsors-title">Met dank aan onze partners</h3>

            <div class="marquee-wrapper">
                <div class="marquee-content">
                    <?php
                    // Check for Manual Sponsors (1-20)
                    $manual_sponsors = [];
                    for ($j = 1; $j <= 20; $j++) {
                        $img_id = get_option("crafted_footer_sponsor_{$j}_img");
                        $url = get_option("crafted_footer_sponsor_{$j}_url");

                        if ($img_id) {
                            $thumb = wp_get_attachment_image_url($img_id, 'medium');
                            if ($thumb) {
                                if ($url) {
                                    $manual_sponsors[] = '<a href="' . esc_url($url) . '" target="_blank" class="sponsor-pill clickable"><img src="' . esc_url($thumb) . '" alt="Sponsor"></a>';
                                } else {
                                    $manual_sponsors[] = '<div class="sponsor-pill"><img src="' . esc_url($thumb) . '" alt="Sponsor"></div>';
                                }
                            }
                        }
                    }

                    $final_items = [];

                    if (!empty($manual_sponsors)) {
                        // Use Manual Sponsors
                        $final_items = $manual_sponsors;
                    } else {
                        // Fallback to CPT
                        $orgs = new WP_Query([
                            'post_type' => 'organisatie',
                            'posts_per_page' => -1,
                            'orderby' => 'menu_order',
                            'order' => 'ASC'
                        ]);

                        if ($orgs->have_posts()) {
                            while ($orgs->have_posts()) {
                                $orgs->the_post();
                                $img_id = get_post_meta(get_the_ID(), 'organisatie_image', true);
                                if (!$img_id) {
                                    $img_id = get_post_thumbnail_id(get_the_ID());
                                }
                                $thumb = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : '';

                                if ($thumb) {
                                    $final_items[] = '<div class="sponsor-pill"><img src="' . esc_url($thumb) . '" alt="' . get_the_title() . '"> <span class="sponsor-name">' . get_the_title() . '</span></div>';
                                } else {
                                    $final_items[] = '<div class="sponsor-pill"><span class="sponsor-name">' . get_the_title() . '</span></div>';
                                }
                            }
                            wp_reset_postdata();
                        }
                    }

                    // Duplicate items for infinite scroll
                    if (count($final_items) > 0) {
                        // Ensure enough items for smooth scroll
                        $display_items = $final_items;
                        while (count($display_items) < 10) {
                            $display_items = array_merge($display_items, $final_items);
                        }
                        echo implode('', $display_items);
                        echo implode('', $display_items); // Double up
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Wave SVG Animation (Moved Outside Wrapper) -->
    <div class="footer-wave-container">
        <svg class="footer-wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1077 59"
             preserveAspectRatio="none">
            <path d="M0 8.71041C269.25 38.8842 538.5 -21.4633 1077 8.71041V59H0V8.71041Z" fill="url(#wave_gradient)"
                  fill-opacity="0.2" />
            <defs>
                <linearGradient id="wave_gradient" x1="0" y1="0" x2="100%" y2="0" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#77357C" />
                    <stop offset="0.5" stop-color="#C25A95" />
                    <stop offset="1" stop-color="#E27391" />
                </linearGradient>
            </defs>
        </svg>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>