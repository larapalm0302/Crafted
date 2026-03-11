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
                        'instagram' => [
                            'url' => get_option('crafted_social_insta'),
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 51 51" class="icon"><path d="M36.125 4.25H14.875C9.00697 4.25 4.25 9.00697 4.25 14.875V36.125C4.25 41.993 9.00697 46.75 14.875 46.75H36.125C41.993 46.75 46.75 41.993 46.75 36.125V14.875C46.75 9.00697 41.993 4.25 36.125 4.25Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M34.0001 24.1613C34.2624 25.9298 33.9603 27.736 33.1368 29.3229C32.3134 30.9099 31.0105 32.1968 29.4136 33.0006C27.8166 33.8044 26.0068 34.0842 24.2417 33.8001C22.4765 33.5161 20.8459 32.6827 19.5816 31.4185C18.3174 30.1543 17.484 28.5236 17.2 26.7585C16.916 24.9933 17.1957 23.1836 17.9996 21.5866C18.8034 19.9896 20.0903 18.6867 21.6772 17.8633C23.2641 17.0399 25.0703 16.7378 26.8389 17C28.6428 17.2675 30.3129 18.1081 31.6025 19.3977C32.892 20.6872 33.7326 22.3573 34.0001 24.1613Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M37.1875 13.8125H37.2081" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>'
                        ],
                        'linkedin' => [
                            'url' => get_option('crafted_social_linkedin'),
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 52 52" class="icon"><g clip-path="url(#linkedin-clip-footer)"><path d="M34.6667 17.3333C38.1146 17.3333 41.4212 18.703 43.8591 21.1409C46.2971 23.5789 47.6667 26.8855 47.6667 30.3333V45.5H39.0001V30.3333C39.0001 29.184 38.5435 28.0818 37.7309 27.2692C36.9182 26.4565 35.816 26 34.6667 26C33.5175 26 32.4153 26.4565 31.6026 27.2692C30.79 28.0818 30.3334 29.184 30.3334 30.3333V45.5H21.6667V30.3333C21.6667 26.8855 23.0364 23.5789 25.4744 21.1409C27.9123 18.703 31.2189 17.3333 34.6667 17.3333Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M12.9999 19.5H4.33325V45.5H12.9999V19.5Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M8.66659 13C11.0598 13 12.9999 11.0599 12.9999 8.66665C12.9999 6.27341 11.0598 4.33331 8.66659 4.33331C6.27335 4.33331 4.33325 6.27341 4.33325 8.66665C4.33325 11.0599 6.27335 13 8.66659 13Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></g><defs><clipPath id="linkedin-clip-footer"><rect width="52" height="52" /></clipPath></defs></svg>'
                        ],
                        'tiktok' => [
                            'url' => get_option('crafted_social_tiktok'),
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" viewBox="0 0 682.667 682.667" class="my-icon"><g><defs><clipPath id="tiktok-clip-footer" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" /></clipPath></defs><g clip-path="url(#tiktok-clip-footer)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0c-62.115 0-112.467 50.353-112.467 112.466h-80.334V-216.9c0-39.93-32.369-72.3-72.301-72.3-39.929 0-72.298 32.37-72.298 72.3 0 39.929 32.369 72.299 72.298 72.299v80.334c-84.294 0-152.632-68.337-152.632-152.633 0-84.297 68.338-152.634 152.632-152.634 84.301 0 152.635 68.337 152.635 152.634v172.761C-80.811-66.912-41.975-80.334 0-80.334Z" transform="translate(464.867 384.534)" fill="none" stroke="currentColor" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" /></g></g></svg>'
                        ],
                        'youtube' => [
                            'url' => get_option('crafted_social_youtube'),
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 51 51" class="icon"><path d="M5.31258 36.125C3.8281 29.1194 3.8281 21.8806 5.31258 14.875C5.50763 14.1636 5.88449 13.5152 6.40611 12.9935C6.92774 12.4719 7.57615 12.095 8.28758 11.9C19.6849 10.0118 31.3152 10.0118 42.7126 11.9C43.424 12.095 44.0724 12.4719 44.594 12.9935C45.1157 13.5152 45.4925 14.1636 45.6876 14.875C47.1721 21.8806 47.1721 29.1194 45.6876 36.125C45.4925 36.8364 45.1157 37.4848 44.594 38.0065C44.0724 38.5281 43.424 38.9049 42.7126 39.1C31.3153 40.9885 19.6849 40.9885 8.28758 39.1C7.57615 38.9049 6.92774 38.5281 6.40611 38.0065C5.88449 37.4848 5.50763 36.8364 5.31258 36.125Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /><path d="M21.25 31.875L31.875 25.5L21.25 19.125V31.875Z" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>'
                        ]
                    ];

                    foreach ($socials as $key => $data):
                        if ($data['url']):
                            ?>
                    <a href="<?= esc_url($data['url']) ?>" target="_blank" class="social-circle-btn" aria-label="<?= ucfirst($key) ?>">
                        <?= $data['svg'] ?>
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

        <div class="footer-bottom-bar">
            <p class="footer-copyright">&copy; <?php echo date('Y'); ?> CRAFTED</p>
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('credits'))); ?>" class="footer-credits-link">Credits</a>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
  var link = document.querySelector('.footer-credits-link');
  if (!link) return;
  link.addEventListener('click', function(e) {
    e.preventDefault();
    var href = this.href;
    var rect = this.getBoundingClientRect();
    var cx = rect.left + rect.width / 2;
    var cy = rect.top + rect.height / 2;
    for (var i = 0; i < 10; i++) {
      var dot = document.createElement('span');
      dot.style.cssText = 'position:fixed;width:6px;height:6px;border-radius:50%;pointer-events:none;z-index:9999;left:' + cx + 'px;top:' + cy + 'px;background:' + ['#773570','#C25A95','#E27391','#FFEA99','#F5A986'][i % 5];
      var angle = (i / 10) * Math.PI * 2;
      var dist = 40 + Math.random() * 50;
      var tx = Math.cos(angle) * dist;
      var ty = Math.sin(angle) * dist;
      dot.animate([{transform:'translate(0,0) scale(1)',opacity:1},{transform:'translate('+tx+'px,'+ty+'px) scale(0)',opacity:0}],{duration:600,easing:'cubic-bezier(0.22,1,0.36,1)'});
      document.body.appendChild(dot);
      setTimeout(function(d){d.remove();},650,dot);
    }
    this.classList.add('clicked');
    setTimeout(function() { window.location.href = href; }, 800);
  });
});
</script>
</body>

</html>