<?php

get_header();

function crafted_get_image_url($post_id, $meta_key, $size = 'medium')
{
    $img_id = get_post_meta($post_id, $meta_key, true);
    if ($img_id) {
        return wp_get_attachment_image_url($img_id, $size);
    }
    return get_the_post_thumbnail_url($post_id, $size);
}

?>

<div class="crafted-friends-page">

    <!-- HERO / TITLE -->
    <div style="text-align: center; padding: 40px 20px;">
        <h2 style="font-family:'Inter',sans-serif; font-size: 3rem; color: #7b377d; margin-bottom: 20px; font-weight:700;">Crafted And Friends</h2>
    </div>

    <!-- WIE / WAT (Yellow Block) -->
    <section class="crafted-wiewat">
        <div>
            <h2>Wat?</h2>
            <p>Een evenement waar studenten van verschillende scholen hun talent laten zien. Van mode tot muziek, van techniek tot design. Samen maken we een feest!</p>
        </div>
        <div>
            <h2>Wie?</h2>
            <p>Studenten van verschillende scholen werken samen aan dit evenement. Iedereen brengt zijn eigen expertise en creativiteit.</p>
        </div>
    </section>

    <!-- SCHOLEN SECTION (Note: Singular .school-section matches CSS) -->
    <section class="school-section">
        <h2 class="section-title">Deelnemende Scholen</h2>
        <p class="section-subtitle">Elk team brengt hun unieke expertise en creativiteit.</p>

        <div class="crafted-schools-list">
            <?php
            $schools = new WP_Query([
                'post_type' => 'school',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC'
            ]);

            if ($schools->have_posts()):
                while ($schools->have_posts()): $schools->the_post();
                    $subtitle = get_post_meta(get_the_ID(), 'school_subtitle', true);
                    $link = get_post_meta(get_the_ID(), 'school_link', true);
                    $icon_url = get_post_meta(get_the_ID(), 'school_icon_url', true);
                    $extended_info = get_post_meta(get_the_ID(), 'school_extended_info', true);
                    $desc = get_the_content();

                    $thumb = crafted_get_image_url(get_the_ID(), 'school_image', 'medium-large');
            ?>
                    <div class="crafted-school-item">
                        <div class="crafted-school-image">
                            <?php if ($thumb): ?><img src="<?= esc_url($thumb) ?>" alt="<?= get_the_title() ?>"><?php endif; ?>
                            <?php if ($icon_url): ?>
                                <div class="school-overlay">
                                    <img src="<?= esc_url($icon_url) ?>" class="school-overlay-icon" alt="icon">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="crafted-school-content">
                            <h3><?= get_the_title() ?></h3>
                            <?php if ($subtitle): ?><h4><?= esc_html($subtitle) ?></h4><?php endif; ?>
                            <?php if ($desc): ?><div class="school-desc"><?= wp_kses_post($desc) ?></div><?php endif; ?>
                            
                            <?php if (!empty($extended_info)): ?>
                                <!-- Show Modal Button if there is extended info -->
                                <button class="crafted-button crafted-school-modal-btn">Meer informatie</button>
                                <!-- Hidden payload for JS to read -->
                                <div class="school-payload-data" style="display:none;">
                                    <div class="sp-title"><?= esc_html(get_the_title()) ?></div>
                                    <div class="sp-image"><?= esc_url($thumb) ?></div>
                                    <div class="sp-content"><?= wp_kses_post($extended_info) ?></div>
                                    <?php if ($link): ?><div class="sp-link"><?= esc_url($link) ?></div><?php endif; ?>
                                </div>
                            <?php elseif ($link): ?>
                                <!-- Fallback to old external link behavior if no extended info -->
                                <a class="crafted-button" href="<?= esc_url($link) ?>" target="_blank">Meer informatie</a>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php endwhile;
                wp_reset_postdata();
            else:
                echo '<p>Geen scholen gevonden.</p>';
            endif;
            ?>
        </div>
    </section>

    <!-- ORGANISATIES SECTION -->
    <div class="crafted-grid-wrapper org-section">
        <div class="crafted-grid-header">
            <h2>Organisatie</h2>
            <p class="crafted-subtitle">Het team achter de schermen dat alles mogelijk maakt.</p>
        </div>

        <div class="crafted-carousel-container">
            <button class="carousel-btn carousel-prev" aria-label="Vorige">‹</button>
            <div class="crafted-carousel">
                <?php
                $orgs = new WP_Query([
                    'post_type' => 'organisatie',
                    'posts_per_page' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'ASC'
                ]);

                if ($orgs->have_posts()):
                    while ($orgs->have_posts()): $orgs->the_post();
                        $thumb = crafted_get_image_url(get_the_ID(), 'organisatie_image', 'medium');
                ?>
                        <div class="crafted-grid-item">
                            <div class="crafted-grid-image">
                                <?php if ($thumb): ?><img src="<?= esc_url($thumb) ?>" alt="<?= get_the_title() ?>"><?php endif; ?>
                            </div>
                            <div class="crafted-grid-content">
                                <h4><?= get_the_title() ?></h4>
                                <div class="org-desc"><?= get_the_content() ?></div>
                            </div>
                        </div>
                <?php endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <button class="carousel-btn carousel-next" aria-label="Volgende">›</button>
        </div>
    </div>

    <!-- AMBASSADEURS SECTION -->
    <div class="crafted-grid-wrapper amb-section">
        <div class="crafted-grid-header">
            <h2>Ambassadeurs</h2>
            <p class="crafted-subtitle">Onze ambassadeurs zijn het gezicht van Crafted & Friends.</p>
        </div>

        <div class="crafted-carousel-container">
            <button class="carousel-btn carousel-prev" aria-label="Vorige">‹</button>
            <div class="crafted-carousel">
                <?php
                $ambs = new WP_Query([
                    'post_type' => 'ambassadeur',
                    'posts_per_page' => -1
                ]);

                if ($ambs->have_posts()):
                    while ($ambs->have_posts()): $ambs->the_post();
                        $thumb = crafted_get_image_url(get_the_ID(), 'ambassadeur_image', 'medium');
                        $quote = get_post_meta(get_the_ID(), 'ambassadeur_quote', true);
                ?>
                        <div class="crafted-grid-item ambassador-item">
                            <div class="crafted-grid-image">
                                <?php if ($thumb): ?><img src="<?= esc_url($thumb) ?>" alt="<?= get_the_title() ?>"><?php endif; ?>
                            </div>
                            <div class="crafted-grid-content">
                                <h4><?= get_the_title() ?></h4>
                                <?php if ($quote): ?>
                                    <blockquote class="crafted-quote">&ldquo;<?= esc_html($quote) ?>&rdquo;</blockquote>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <button class="carousel-btn carousel-next" aria-label="Volgende">›</button>
        </div>
    </div>

</div>

<!-- The School Modal -->
<div id="crafted-school-modal" class="crafted-modal-overlay">
    <div class="crafted-modal-box">
        <button class="crafted-modal-close" aria-label="Sluiten">&times;</button>
        <div class="crafted-modal-header-img">
            <img id="modal-school-img" src="" alt="School Header">
        </div>
        <div class="crafted-modal-body">
            <h2 id="modal-school-title">School Titel</h2>
            <div id="modal-school-content" class="modal-formatted-content"></div>
            <a id="modal-school-link" class="crafted-button modal-external-btn" href="#" target="_blank" style="display:none; margin-top:20px;">Meer info? Klik deze knop</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('crafted-school-modal');
    // Ensure modal exists to avoid errors on pages without schools
    if (!modal) return;
    
    const closeBtn = document.querySelector('.crafted-modal-close');
    const modalImg = document.getElementById('modal-school-img');
    const modalTitle = document.getElementById('modal-school-title');
    const modalContent = document.getElementById('modal-school-content');
    const modalLink = document.getElementById('modal-school-link');

    // Open Modal
    document.querySelectorAll('.crafted-school-modal-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const payload = this.nextElementSibling;
            if (!payload) return;
            
            const title = payload.querySelector('.sp-title')?.innerHTML || '';
            const image = payload.querySelector('.sp-image')?.innerHTML || '';
            const content = payload.querySelector('.sp-content')?.innerHTML || '';
            const link = payload.querySelector('.sp-link')?.innerHTML || '';

            modalTitle.innerHTML = title;
            modalContent.innerHTML = content;
            
            if (image) {
                modalImg.src = image;
                modalImg.style.display = 'block';
            } else {
                modalImg.style.display = 'none';
            }

            if (link) {
                modalLink.href = link;
                modalLink.style.display = 'inline-block';
            } else {
                modalLink.style.display = 'none';
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        });
    });

    // Close Modal
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    if(closeBtn) closeBtn.addEventListener('click', closeModal);
    
    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
});
</script>

<?php get_footer(); ?>