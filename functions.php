<?php
function register_programma_posttype() {
    register_post_type('programma', [
        'labels' => [
            'name' => 'Programma',
            'singular_name' => 'Programma item',
            'add_new' => 'Nieuw item',
            'add_new_item' => 'Nieuw programma item',
            'edit_item' => 'Bewerk item',
            'view_item' => 'Bekijk item',
            'all_items' => 'Alle items',
        ],
        'public' => true,
        'has_archive' => false,
        'publicly_queryable' => false,
        'menu_icon' => 'dashicons-calendar',
        'supports' => ['title', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_programma_posttype');

function programma_add_meta_boxes() {
    add_meta_box(
        'programma_details',
        'Programma Details',
        'programma_meta_box_callback',
        'programma',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'programma_add_meta_boxes');

function programma_meta_box_callback($post) {
    wp_nonce_field('programma_save_meta', 'programma_meta_nonce');
    
    $start_time = get_post_meta($post->ID, 'start_time', true);
    $end_time = get_post_meta($post->ID, 'end_time', true);
    $description = get_post_meta($post->ID, 'description', true);
    $image_id = get_post_meta($post->ID, 'programma_image', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
    ?>
<table class="form-table">
    <th><label for="start_time">Starttijd</label></th>
    <td><input type="time" name="start_time" id="start_time" value="<?php echo esc_attr($start_time); ?>"></td>
    </tr>
    <tr>
        <th><label for="end_time">Eindtijd</label></th>
        <td><input type="time" name="end_time" id="end_time" value="<?php echo esc_attr($end_time); ?>"></td>
    </tr>
    <tr>
        <th><label for="description">Beschrijving</label></th>
        <td><textarea name="description" id="description" rows="4" style="width: 100%;"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label>Afbeelding</label></th>
        <td>
            <div id="programma-image-preview" style="margin-bottom: 10px;">
                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 200px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <input type="hidden" name="programma_image" id="programma_image" value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button" id="upload-image-btn">Afbeelding uploaden</button>
            <button type="button" class="button" id="remove-image-btn" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Verwijderen</button>
        </td>
    </tr>
</table>

<script>
jQuery(document).ready(function($) {
    var frame;
    $('#upload-image-btn').on('click', function(e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Selecteer afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#programma_image').val(attachment.id);
            $('#programma-image-preview').html('<img src="' + attachment.sizes.medium.url + '" style="max-width: 200px; height: auto; border-radius: 8px;">');
            $('#remove-image-btn').show();
        });
        frame.open();
    });
    $('#remove-image-btn').on('click', function(e) {
        e.preventDefault();
        $('#programma_image').val('');
        $('#programma-image-preview').html('');
        $(this).hide();
    });
});
</script>
<?php
}

function programma_save_meta($post_id) {
    if (!isset($_POST['programma_meta_nonce']) || !wp_verify_nonce($_POST['programma_meta_nonce'], 'programma_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = ['datum', 'start_time', 'end_time', 'description'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    if (isset($_POST['programma_image'])) {
        update_post_meta($post_id, 'programma_image', absint($_POST['programma_image']));
    }
}
add_action('save_post_programma', 'programma_save_meta');

function programma_set_required_notice($message) {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }
    set_transient('programma_required_notice_' . $user_id, $message, 60);
}

function programma_validate_required_fields($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if ($post->post_status === 'trash') {
        return;
    }

    $missing = [];
    if (trim((string) $post->post_title) === '') {
        $missing[] = 'titel';
    }
    $description = trim((string) get_post_meta($post_id, 'description', true));
    if ($description === '') {
        $missing[] = 'beschrijving';
    }
    $image_id = get_post_meta($post_id, 'programma_image', true);
    if (!$image_id) {
        $missing[] = 'afbeelding';
    }

    if (!$missing) {
        return;
    }

    if ($post->post_status === 'publish') {
        remove_action('save_post_programma', 'programma_validate_required_fields', 20);
        wp_update_post([
            'ID' => $post_id,
            'post_status' => 'draft',
        ]);
        add_action('save_post_programma', 'programma_validate_required_fields', 20, 3);
    }

    programma_set_required_notice('Vul verplicht: ' . implode(', ', $missing) . '.');
}
add_action('save_post_programma', 'programma_validate_required_fields', 20, 3);

function programma_render_required_notice() {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }
    $notice = get_transient('programma_required_notice_' . $user_id);
    if (!$notice) {
        return;
    }
    delete_transient('programma_required_notice_' . $user_id);
    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($notice) . '</p></div>';
}
add_action('admin_notices', 'programma_render_required_notice');

function programma_enqueue_admin_scripts($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post;
        if ($post && $post->post_type === 'programma') {
            wp_enqueue_media();
        }
    }
}
add_action('admin_enqueue_scripts', 'programma_enqueue_admin_scripts');

function get_field($field, $post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, $field, true);
}

function crafted_contact_settings_init() {
    register_setting('crafted_contact', 'crafted_contact_email');
    register_setting('crafted_contact', 'crafted_contact_phone');
    register_setting('crafted_contact', 'crafted_contact_address');
    
    add_settings_section(
        'crafted_contact_section',
        'Contact Informatie',
        null,
        'crafted_contact'
    );
    
    add_settings_field(
        'crafted_contact_email',
        'E-mailadres',
        'crafted_contact_email_field',
        'crafted_contact',
        'crafted_contact_section'
    );
    
    add_settings_field(
        'crafted_contact_phone',
        'Telefoonnummer',
        'crafted_contact_phone_field',
        'crafted_contact',
        'crafted_contact_section'
    );
    
    add_settings_field(
        'crafted_contact_address',
        'Adres',
        'crafted_contact_address_field',
        'crafted_contact',
        'crafted_contact_section'
    );
}
add_action('admin_init', 'crafted_contact_settings_init');

function crafted_contact_email_field() {
    $value = get_option('crafted_contact_email', 'crafted@summacollege.nl');
    echo '<input type="email" name="crafted_contact_email" value="' . esc_attr($value) . '" class="regular-text">';
}

function crafted_contact_phone_field() {
    $value = get_option('crafted_contact_phone', 'Telefoonnummer hier');
    echo '<input type="text" name="crafted_contact_phone" value="' . esc_attr($value) . '" class="regular-text">';
}

function crafted_contact_address_field() {
    $value = get_option('crafted_contact_address', 'Klokgebouw 50, 5617 AB Eindhoven');
    echo '<input type="text" name="crafted_contact_address" value="' . esc_attr($value) . '" class="regular-text">';
}

function crafted_contact_menu() {
    add_menu_page(
        'Contact Info',
        'Contact Info',
        'manage_options',
        'crafted-contact',
        'crafted_contact_page',
        'dashicons-phone',
        30
    );
}
add_action('admin_menu', 'crafted_contact_menu');

function crafted_contact_page() {
    ?>
<div class="wrap">
    <h1>Contact Informatie</h1>
    <form method="post" action="options.php">
        <?php
            settings_fields('crafted_contact');
            do_settings_sections('crafted_contact');
            submit_button();
            ?>
    </form>
</div>
<?php
}

function add_styles() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Krona+One&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
}

add_action('wp_enqueue_scripts', 'add_styles');

// --- Enqueue Frontend Assets ---
function crafted_friends_enqueue_assets()
{
    wp_enqueue_style('crafted-main-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('crafted-blocks-style', get_template_directory_uri() . '/assets/css/style-blocks.css', [], '2.9');
    wp_enqueue_script('crafted-carousel', get_template_directory_uri() . '/assets/js/carousel.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'crafted_friends_enqueue_assets');

// --- Admin Scripts (voor alle CPT's) ---
function crafted_admin_scripts($hook)
{
    global $post;
    $allowed_types = ['programma', 'school', 'organisatie', 'ambassadeur', 'livestream'];
    $is_footer_page = (isset($_GET['page']) && $_GET['page'] === 'crafted-footer');

    if ($is_footer_page || (($hook === 'post.php' || $hook === 'post-new.php') && $post && in_array($post->post_type, $allowed_types))) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'crafted_admin_scripts');

function crafted_admin_footer_scripts()
{
    global $post;
    $allowed_types = ['programma', 'school', 'organisatie', 'ambassadeur', 'livestream'];
    $is_footer_page = (isset($_GET['page']) && $_GET['page'] === 'crafted-footer');

    if (!$is_footer_page && (!$post || !in_array($post->post_type, $allowed_types)))
        return;
    ?>
<script>
jQuery(document).ready(function($) {
    $('.upload-image-btn').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var frame = wp.media({
            title: 'Selecteer afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            var targetId = btn.data('target');
            var previewId = btn.data('preview');
            $('#' + targetId).val(attachment.id);
            $('#' + previewId).html('<img src="' + attachment.sizes.medium.url + '" style="max-height: 150px; width: auto; border-radius: 8px;">');
            btn.next('.remove-image-btn').show();
        });
        frame.open();
    });
    $('.remove-image-btn').on('click', function(e) {
        e.preventDefault();
        var targetId = $(this).data('target');
        var previewId = $(this).data('preview');
        $('#' + targetId).val('');
        $('#' + previewId).html('');
        $(this).hide();
    });
});
</script>
<?php
}
add_action('admin_footer', 'crafted_admin_footer_scripts');

// --- Nieuwe Custom Post Types ---
function crafted_register_extra_cpts()
{
    // School
    register_post_type('school', [
        'labels' => ['name' => 'Scholen', 'singular_name' => 'School', 'add_new_item' => 'Nieuwe School'],
        'public' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
    // Organisatie
    register_post_type('organisatie', [
        'labels' => ['name' => 'Organisaties', 'singular_name' => 'Organisatie', 'add_new_item' => 'Nieuwe Organisatie'],
        'public' => true,
        'menu_icon' => 'dashicons-groups',
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
    // Ambassadeur
    register_post_type('ambassadeur', [
        'labels' => ['name' => 'Ambassadeurs', 'singular_name' => 'Ambassadeur', 'add_new_item' => 'Nieuwe Ambassadeur'],
        'public' => true,
        'menu_icon' => 'dashicons-businessman',
        'supports' => ['title', 'thumbnail'],
    ]);
    // Livestream
    register_post_type('livestream', [
        'labels' => [
            'name' => 'Livestream',
            'singular_name' => 'Livestream',
            'add_new_item' => 'Nieuwe Livestream Instellen',
            'edit_item' => 'Livestream Bewerken'
        ],
        'public' => true,
        'menu_icon' => 'dashicons-video-alt3',
        'supports' => ['title'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'crafted_register_extra_cpts');

// --- Meta Boxes voor alle CPT's ---
function crafted_add_extra_meta_boxes()
{
    add_meta_box('school_details', 'School Details & Afbeelding', 'school_meta_callback', 'school', 'normal', 'high');
    add_meta_box('organisatie_details', 'Organisatie Afbeelding', 'organisatie_meta_callback', 'organisatie', 'normal', 'high');
    add_meta_box('ambassadeur_details', 'Ambassadeur Details & Afbeelding', 'ambassadeur_meta_callback', 'ambassadeur', 'normal', 'high');
    add_meta_box('livestream_details', 'Livestream Configuratie', 'livestream_meta_callback', 'livestream', 'normal', 'high');
}
add_action('add_meta_boxes', 'crafted_add_extra_meta_boxes');

// --- Helper Function voor Image Upload ---
function crafted_render_image_field($meta_key, $post_id, $label)
{
    if ($post_id === 0) {
        $image_id = get_option($meta_key);
    } else {
        $image_id = get_post_meta($post_id, $meta_key, true);
    }

    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
    ?>
<p><strong><?= $label ?></strong></p>
<div id="<?= $meta_key ?>_preview" style="margin-bottom: 10px;">
    <?php if ($image_url): ?><img src="<?= esc_url($image_url) ?>"
         style="max-width: 200px; height: auto; border-radius: 8px;"><?php endif; ?>
</div>
<input type="hidden" name="<?= $meta_key ?>" id="<?= $meta_key ?>" value="<?= esc_attr($image_id) ?>">
<button type="button" class="button upload-image-btn" data-target="<?= $meta_key ?>"
        data-preview="<?= $meta_key ?>_preview">Afbeelding kiezen</button>
<button type="button" class="button remove-image-btn" data-target="<?= $meta_key ?>"
        data-preview="<?= $meta_key ?>_preview" style="<?= $image_id ? '' : 'display:none;' ?>">Verwijderen</button>
<?php
}

// --- Meta Box Callbacks ---
function school_meta_callback($post)
{
    wp_nonce_field('crafted_save_meta', 'crafted_meta_nonce');
    $subtitle = get_post_meta($post->ID, 'school_subtitle', true);
    $link = get_post_meta($post->ID, 'school_link', true);
    $icon_url = get_post_meta($post->ID, 'school_icon_url', true);

    echo '<p><label>Subtitel:</label><br><input type="text" name="school_subtitle" value="' . esc_attr($subtitle) . '" style="width:100%"></p>';
    echo '<p><label>Link:</label><br><input type="text" name="school_link" value="' . esc_attr($link) . '" style="width:100%"></p>';
    echo '<p><label>Icoon URL (voor overlay):</label><br><input type="text" name="school_icon_url" value="' . esc_attr($icon_url) . '" style="width:100%" placeholder="https://..."></p>';
    crafted_render_image_field('school_image', $post->ID, 'School Hoofdafbeelding');
}

function organisatie_meta_callback($post)
{
    wp_nonce_field('crafted_save_meta', 'crafted_meta_nonce');
    crafted_render_image_field('organisatie_image', $post->ID, 'Organisatie Logo/Afbeelding');
}

function ambassadeur_meta_callback($post)
{
    wp_nonce_field('crafted_save_meta', 'crafted_meta_nonce');
    $quote = get_post_meta($post->ID, 'ambassadeur_quote', true);
    echo '<p><label>Quote:</label><br><textarea name="ambassadeur_quote" style="width:100%" rows="3">' . esc_textarea($quote) . '</textarea></p>';
    crafted_render_image_field('ambassadeur_image', $post->ID, 'Ambassadeur Foto');
}

function livestream_meta_callback($post)
{
    wp_nonce_field('crafted_save_meta', 'crafted_meta_nonce');
    $video_url = get_post_meta($post->ID, 'ls_video_url', true);
    $back_url = get_post_meta($post->ID, 'ls_back_url', true);
    $channel_name = get_post_meta($post->ID, 'ls_channel_name', true);
    $is_live = get_post_meta($post->ID, 'ls_is_live', true);
    ?>
<p><strong>Livestream Configuratie</strong></p>
<p>
    <label>
        <input type="checkbox" name="ls_is_live" value="1" <?php checked($is_live, '1'); ?>>
        <strong>Is dit een LIVE stream?</strong> (Toont LIVE icoon)
    </label>
</p>
<p>
    <label>YouTube Link (Video URL):</label><br>
    <input type="text" name="ls_video_url" value="<?= esc_attr($video_url) ?>" style="width:100%" placeholder="https://youtube.com/watch?v=...">
</p>
<p>
    <label>Kanaal Naam:</label><br>
    <input type="text" name="ls_channel_name" value="<?= esc_attr($channel_name) ?>" style="width:100%" placeholder="Bijv. CRAFTED TV">
</p>
<?php crafted_render_image_field('ls_logo_url_id', $post->ID, 'Kanaal Logo (Rond plaatje):'); ?>
<p>
    <label>Terugknop URL:</label><br>
    <input type="text" name="ls_back_url" value="<?= esc_attr($back_url) ?>" style="width:100%" placeholder="/">
</p>
<?php
}

// --- Save Extra Meta Data ---
function crafted_save_extra_meta_data($post_id)
{
    if (!isset($_POST['crafted_meta_nonce']) || !wp_verify_nonce($_POST['crafted_meta_nonce'], 'crafted_save_meta'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    // Text Fields
    $text_fields = [
        'school_subtitle', 'school_link', 'school_icon_url',
        'ambassadeur_quote',
        'ls_video_url', 'ls_channel_name', 'ls_back_url'
    ];
    foreach ($text_fields as $field) {
        if (isset($_POST[$field]))
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
    }

    // Checkbox
    update_post_meta($post_id, 'ls_is_live', isset($_POST['ls_is_live']) ? '1' : '0');

    // Image Fields
    $image_fields = ['school_image', 'organisatie_image', 'ambassadeur_image', 'ls_logo_url_id'];
    foreach ($image_fields as $field) {
        if (isset($_POST[$field]))
            update_post_meta($post_id, $field, absint($_POST[$field]));
    }
}
add_action('save_post', 'crafted_save_extra_meta_data');

// --- Footer Menu Pagina ---
function crafted_footer_menu()
{
    add_menu_page('Footer Opties', 'Footer Opties', 'manage_options', 'crafted-footer', 'crafted_footer_page', 'dashicons-layout', 31);
}
add_action('admin_menu', 'crafted_footer_menu');

function crafted_footer_page()
{
    echo '<div class="wrap"><h1>Footer Instellingen</h1><form method="post" action="options.php">';
    settings_fields('crafted_footer_group');
    do_settings_sections('crafted_footer');
    submit_button();
    echo '</form></div>';
}

// --- Footer Settings ---
function crafted_footer_settings_init()
{
    add_settings_section('crafted_footer_section', 'Footer Instellingen', '__return_false', 'crafted_footer');

    // Social Media
    register_setting('crafted_footer_group', 'crafted_social_insta');
    register_setting('crafted_footer_group', 'crafted_social_linkedin');
    register_setting('crafted_footer_group', 'crafted_social_tiktok');
    register_setting('crafted_footer_group', 'crafted_social_youtube');

    add_settings_field('crafted_social_insta', 'Instagram URL', 'crafted_social_insta_cb', 'crafted_footer', 'crafted_footer_section');
    add_settings_field('crafted_social_linkedin', 'LinkedIn URL', 'crafted_social_linkedin_cb', 'crafted_footer', 'crafted_footer_section');
    add_settings_field('crafted_social_tiktok', 'TikTok URL', 'crafted_social_tiktok_cb', 'crafted_footer', 'crafted_footer_section');
    add_settings_field('crafted_social_youtube', 'YouTube URL', 'crafted_social_youtube_cb', 'crafted_footer', 'crafted_footer_section');

    // Quick Links Buttons
    for ($i = 1; $i <= 4; $i++) {
        register_setting('crafted_footer_group', "crafted_footer_btn_{$i}_text");
        register_setting('crafted_footer_group', "crafted_footer_btn_{$i}_url");
        add_settings_field(
            "crafted_footer_btn_{$i}",
            "Button $i (Text & URL)",
            function () use ($i) { crafted_footer_btn_cb($i); },
            'crafted_footer',
            'crafted_footer_section'
        );
    }

    // Sponsors (20 slots)
    add_settings_section('crafted_footer_sponsors_section', 'Sponsoren (Handmatig)', '__return_false', 'crafted_footer');
    for ($j = 1; $j <= 20; $j++) {
        register_setting('crafted_footer_group', "crafted_footer_sponsor_{$j}_img");
        register_setting('crafted_footer_group', "crafted_footer_sponsor_{$j}_url");
        add_settings_field(
            "crafted_footer_sponsor_{$j}",
            "Sponsor $j",
            function () use ($j) {
                crafted_render_image_field("crafted_footer_sponsor_{$j}_img", 0, "Logo");
                $url_val = get_option("crafted_footer_sponsor_{$j}_url");
                echo '<p style="margin-top:10px;"><label>Website URL:</label><br>';
                echo '<input type="text" name="crafted_footer_sponsor_' . $j . '_url" value="' . esc_attr($url_val) . '" class="regular-text" placeholder="https://..."></p><hr>';
            },
            'crafted_footer',
            'crafted_footer_sponsors_section'
        );
    }
}
add_action('admin_init', 'crafted_footer_settings_init');

// --- Social Media Callbacks ---
function crafted_social_insta_cb() {
    echo '<input type="text" name="crafted_social_insta" value="' . esc_attr(get_option('crafted_social_insta')) . '" class="regular-text" placeholder="https://instagram.com/...">';
}
function crafted_social_linkedin_cb() {
    echo '<input type="text" name="crafted_social_linkedin" value="' . esc_attr(get_option('crafted_social_linkedin')) . '" class="regular-text" placeholder="https://linkedin.com/...">';
}
function crafted_social_tiktok_cb() {
    echo '<input type="text" name="crafted_social_tiktok" value="' . esc_attr(get_option('crafted_social_tiktok')) . '" class="regular-text" placeholder="https://tiktok.com/...">';
}
function crafted_social_youtube_cb() {
    echo '<input type="text" name="crafted_social_youtube" value="' . esc_attr(get_option('crafted_social_youtube')) . '" class="regular-text" placeholder="https://youtube.com/...">';
}
function crafted_footer_btn_cb($i) {
    $text = get_option("crafted_footer_btn_{$i}_text", "Knop $i");
    $url = get_option("crafted_footer_btn_{$i}_url", "#");
    echo '<input type="text" name="crafted_footer_btn_' . $i . '_text" value="' . esc_attr($text) . '" placeholder="Tekst" style="margin-right:10px;">';
    echo '<input type="text" name="crafted_footer_btn_' . $i . '_url" value="' . esc_attr($url) . '" placeholder="URL" class="regular-text">';
}

// NIEUWS POST TYPE

function register_nieuws_posttype()
{
    register_post_type('nieuws', [
        'labels' => [
            'name' => 'Nieuws',
            'singular_name' => 'Nieuws nieuwsbericht',
            'add_new' => 'Nieuw nieuwsbericht toevoegen',
            'add_new_item' => 'Nieuw nieuwsbericht',
            'edit_item' => 'Bewerk nieuwsbericht',
            'view_item' => 'Bekijk nieuwsbericht',
            'all_items' => 'Alle nieuwsberichten',
            'menu_name'          => 'Nieuws',
            'name_admin_bar'     => 'Nieuwsbericht',
            'search_items'       => 'Zoek nieuws',
            'not_found'          => 'Geen nieuws gevonden',
            'not_found_in_trash' => 'Geen nieuws gevonden in de prullenbak',
        ],
        'public' => true,
        'has_archive' => false,
        // 'publicly_queryable' => false,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => ['title', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => 'nieuws-berichten'
        ],
    ]);
}
add_action('init', 'register_nieuws_posttype');

function nieuws_add_meta_boxes()
{
    add_meta_box(
        'nieuws_details',
        'Nieuws Details',
        'nieuws_meta_box_callback',
        'nieuws',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'nieuws_add_meta_boxes');

function nieuws_meta_box_callback($post)
{
    wp_nonce_field('nieuws_save_meta', 'nieuws_meta_nonce');

    $description = get_post_meta($post->ID, 'description', true);
    $image_id = get_post_meta($post->ID, 'nieuws_image', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
    $rawdatetime = get_post_meta($post->ID, 'raw_nieuws_date', true);
    $datetime = get_post_meta($post->ID, 'nieuws_date', true);
    if (empty($datetime)) {
        $datetime = date_i18n('l j F, H:i');
    }
?>
<table class="form-table">
    <tr>
        <th><label for="description">Beschrijving <span style="color:red;">*</span></label></th>
        <td><textarea name="description" id="description" rows="4" style="width: 100%;"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label>Afbeelding <span style="color:red;">*</span></label></th>
        <td>
            <div id="nieuws-image-preview" style="margin-bottom: 10px;">
                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 200px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <input type="hidden" name="nieuws_image" id="nieuws_image" value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button" id="upload-image-btn">Afbeelding uploaden</button>
            <button type="button" class="button" id="remove-image-btn" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Verwijderen</button>
        </td>
    </tr>
    <tr>
        <th><label for="raw_nieuws_date">Datum & Tijd</label></th>
        <td><input type="datetime-local" name="raw_nieuws_date" id="raw_nieuws_date" value="<?php echo esc_attr($rawdatetime); ?>"></td>
    </tr>
</table>

<script>
jQuery(document).ready(function($) {
    var frame;
    $('#upload-image-btn').on('click', function(e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Selecteer afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#nieuws_image').val(attachment.id);
            $('#nieuws-image-preview').html('<img src="' + attachment.sizes.medium.url + '" style="max-width: 200px; height: auto; border-radius: 8px;">');
            $('#remove-image-btn').show();
        });
        frame.open();
    });
    $('#remove-image-btn').on('click', function(e) {
        e.preventDefault();
        $('#nieuws_image').val('');
        $('#nieuws-image-preview').html('');
        $(this).hide();
    });
});
</script>
<?php
}

function nieuws_save_meta($post_id)
{
    if (!isset($_POST['nieuws_meta_nonce']) || !wp_verify_nonce($_POST['nieuws_meta_nonce'], 'nieuws_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;    }

    if (isset($_POST['description'])) {
        update_post_meta($post_id, 'description', sanitize_textarea_field($_POST['description']));
    }
    if (isset($_POST['nieuws_image'])) {
        update_post_meta($post_id, 'nieuws_image', absint($_POST['nieuws_image']));
    }
    if (isset($_POST['nieuws_date'])) {
        $rdate = sanitize_text_field($_POST['raw_nieuws_date']);
        if (!empty($rdate)) {
            $timestamp = strtotime($rdate);
            if ($timestamp) {
                $formatted_date = date_i18n('l j F, H:i', $timestamp);
                update_post_meta($post_id, 'nieuws_date', $formatted_date);
            } else {
                update_post_meta($post_id, 'nieuws_date', $rdate);
            }
        } else {
            update_post_meta($post_id, 'nieuws_date', '');
        }
    }
    if (isset($_POST['raw_nieuws_date'])) {
         update_post_meta($post_id, 'raw_nieuws_date', sanitize_text_field($_POST['raw_nieuws_date']));
    }
}
add_action('save_post_nieuws', 'nieuws_save_meta');

// TEASERS POST TYPE

function register_teasers_posttype() {
    register_post_type('teasers', [
        'labels' => [
            'name' => 'Teasers',
            'singular_name' => 'Teaser',
            'add_new' => 'Nieuwe teaser toevoegen',
            'add_new_item' => 'Nieuwe teaser',
            'edit_item' => 'Bewerk teaser',
            'view_item' => 'Bekijk teaser',
            'all_items' => 'Alle teasers',
            'menu_name' => 'Teasers',
            'name_admin_bar' => 'Teaser',
            'search_items' => 'Zoek teasers',
            'not_found' => 'Geen teasers gevonden',
            'not_found_in_trash' => 'Geen teasers gevonden in de prullenbak',
        ],
        'public' => true,
       'has_archive' => false,
        // 'publicly_queryable' => false,
        'menu_icon' => 'dashicons-lightbulb',
        'supports' => ['title', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_teasers_posttype');

function add_teasers_meta_boxes() {
    add_meta_box(
        'teasers_details',
        'Teasers Details',
        'teasers_meta_box_callback',
        'teasers',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_teasers_meta_boxes');

function teasers_meta_box_callback($post) {
    wp_nonce_field('teasers_save_meta', 'teasers_meta_nonce');

    $description = get_post_meta($post->ID, 'description', true);
    $image_id = get_post_meta($post->ID, 'teasers_image', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
    $rawdatetime = get_post_meta($post->ID, 'raw_teasers_date', true);
    $datetime = get_post_meta($post->ID, 'teasers_date', true);
    if (empty($datetime)) {
        $datetime = date_i18n('l j F, H:i');
    }
?>
<table class="form-table">
    <tr>
        <th><label for="description">Beschrijving <span style="color:red;">*</span></label></th>
        <td><textarea name="description" id="description" rows="4" style="width: 100%;"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label>Afbeelding <span style="color:red;">*</span></label></th>
        <td>
            <div id="teasers-image-preview" style="margin-bottom: 10px;">
                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 200px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <input type="hidden" name="teasers_image" id="teasers_image" value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button" id="upload-image-btn">Afbeelding uploaden</button>
            <button type="button" class="button" id="remove-image-btn" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Verwijderen</button>
        </td>
    </tr>
    <tr>
        <th><label for="raw_teasers_date">Datum & Tijd</label></th>
        <td><input type="datetime-local" name="raw_teasers_date" id="raw_teasers_date" value="<?php echo esc_attr($rawdatetime); ?>"></td>
    </tr>
</table>
<script>
jQuery(document).ready(function($) {
    var frame;
    $('#upload-image-btn').on('click', function(e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Selecteer afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#teasers_image').val(attachment.id);
            $('#teasers-image-preview').html('<img src="' + attachment.sizes.medium.url + '" style="max-width: 200px; height: auto; border-radius: 8px;">');
            $('#remove-image-btn').show();
        });
        frame.open();
    });
    $('#remove-image-btn').on('click', function(e) {
        e.preventDefault();
        $('#teasers_image').val('');
        $('#teasers-image-preview').html('');
        $(this).hide();
    });
});
</script>
<?php
}

function teasers_save_meta($post_id) {
    if (!isset($_POST['teasers_meta_nonce']) || !wp_verify_nonce($_POST['teasers_meta_nonce'], 'teasers_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['description'])) {
        update_post_meta($post_id, 'description', sanitize_textarea_field($_POST['description']));
    }
    if (isset($_POST['teasers_image'])) {
        update_post_meta($post_id, 'teasers_image', absint($_POST['teasers_image']));
    }
    if (isset($_POST['raw_teasers_date'])) {
        $rdate = sanitize_text_field($_POST['raw_teasers_date']);
        if (!empty($rdate)) {
            $timestamp = strtotime($rdate);
            if ($timestamp) {
                $formatted_date = date_i18n('l j F, H:i', $timestamp);
                update_post_meta($post_id, 'teasers_date', $formatted_date);
            } else {
                update_post_meta($post_id, 'teasers_date', $rdate);
            }
        } else {
            update_post_meta($post_id, 'teasers_date', '');
        }
        update_post_meta($post_id, 'raw_teasers_date', sanitize_text_field($_POST['raw_teasers_date']));
    }
}
add_action('save_post_teasers', 'teasers_save_meta');

// STORYLINES POST TYPE


function register_storylines_posttype() {
    register_post_type('storylines', [
        'labels' => [
            'name' => 'Storylines',
            'singular_name' => 'Storyline',
            'add_new' => 'Nieuwe storyline toevoegen',
            'add_new_item' => 'Nieuwe storyline',
            'edit_item' => 'Bewerk storyline',
            'view_item' => 'Bekijk storyline',
            'all_items' => 'Alle storylines',
            'menu_name' => 'Storylines',
            'name_admin_bar' => 'Storyline',
            'search_items' => 'Zoek storylines',
            'not_found' => 'Geen storylines gevonden',
            'not_found_in_trash' => 'Geen storylines gevonden in de prullenbak',
        ],
        'public' => true,
        'has_archive' => false,
        // 'publicly_queryable' => false,
        'menu_icon' => 'dashicons-book',
        'supports' => ['title', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_storylines_posttype');

function add_storylines_meta_boxes() {
    add_meta_box(
        'storylines_details',
        'Storylines Details',
        'storylines_meta_box_callback',
        'storylines',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_storylines_meta_boxes');

function storylines_meta_box_callback($post) {
    wp_nonce_field('storylines_save_meta', 'storylines_meta_nonce');

    $description = get_post_meta($post->ID, 'description', true);
    $image_id = get_post_meta($post->ID, 'storylines_image', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
    $rawdatetime = get_post_meta($post->ID, 'raw_storylines_date', true);
    $datetime = get_post_meta($post->ID, 'storylines_date', true);
    if (empty($datetime)) {
        $datetime = date_i18n('l j F, H:i');
    }
?>
<table class="form-table">
    <tr>
        <th><label for="description">Beschrijving <span style="color:red;">*</span></label></th>
        <td><textarea name="description" id="description" rows="4" style="width: 100%;"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label>Afbeelding <span style="color:red;">*</span></label></th>
        <td>
            <div id="storylines-image-preview" style="margin-bottom: 10px;">
                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 200px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <input type="hidden" name="storylines_image" id="storylines_image" value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button" id="upload-image-btn">Afbeelding uploaden</button>
            <button type="button" class="button" id="remove-image-btn" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Verwijderen</button>
        </td>
    </tr>
    <tr>
        <th><label for="raw_storylines_date">Datum & Tijd</label></th>
        <td><input type="datetime-local" name="raw_storylines_date" id="raw_storylines_date" value="<?php echo esc_attr($rawdatetime); ?>"></td>
    </tr>
</table>
<script>
jQuery(document).ready(function($) {
    var frame;
    $('#upload-image-btn').on('click', function(e) {
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Selecteer afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#storylines_image').val(attachment.id);
            $('#storylines-image-preview').html('<img src="' + attachment.sizes.medium.url + '" style="max-width: 200px; height: auto; border-radius: 8px;">');
            $('#remove-image-btn').show();
        });
        frame.open();
    });
    $('#remove-image-btn').on('click', function(e) {
        e.preventDefault();
        $('#storylines_image').val('');
        $('#storylines-image-preview').html('');
        $(this).hide();
    });
});
</script>
<?php
}

function storylines_save_meta($post_id) {
    if (!isset($_POST['storylines_meta_nonce']) || !wp_verify_nonce($_POST['storylines_meta_nonce'], 'storylines_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['description'])) {
        update_post_meta($post_id, 'description', sanitize_textarea_field($_POST['description']));
    }
    if (isset($_POST['storylines_image'])) {
        update_post_meta($post_id, 'storylines_image', absint($_POST['storylines_image']));
    }
    if (isset($_POST['raw_storylines_date'])) {
        $rdate = sanitize_text_field($_POST['raw_storylines_date']);
        if (!empty($rdate)) {
            $timestamp = strtotime($rdate);
            if ($timestamp) {
                $formatted_date = date_i18n('l j F, H:i', $timestamp);
                update_post_meta($post_id, 'storylines_date', $formatted_date);
            } else {
                update_post_meta($post_id, 'storylines_date', $rdate);
            }
        } else {
            update_post_meta($post_id, 'storylines_date', '');
        }
        update_post_meta($post_id, 'raw_storylines_date', sanitize_text_field($_POST['raw_storylines_date']));
    }
}
add_action('save_post_storylines', 'storylines_save_meta');


// ADMIN MEDIA UPLOAD FOR CUSTOM POST TYPES

function upload_images_on_posts($hook)
{
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post;
        if ($post) {
            $args = array(
                'public'   => true,
                '_builtin' => false
            );
            $custom_post_types = get_post_types($args);
            if (in_array($post->post_type, $custom_post_types)) {
                wp_enqueue_media();
            }
        }
    }
}
add_action('admin_enqueue_scripts', 'upload_images_on_posts');

function validate_required_fields($post_id, $post, $update) {
    $required_post_types = ['nieuws', 'teasers', 'storylines'];

    if ($update && in_array($post->post_type, $required_post_types)) {
        $errors = [];

        if (empty($post->post_title)) {
            $errors[] = 'Een titel is verplicht.';
        }

        $image_meta_key = $post->post_type . '_image';
        $image = get_post_meta($post_id, $image_meta_key, true);
        if (empty($image)) {
            $errors[] = 'Een afbeelding is verplicht.';
        }

        $description = get_post_meta($post_id, 'description', true);
        if (empty($description)) {
            $errors[] = 'Een beschrijving is verplicht.';
        }

        if (!empty($errors)) {
            wp_die(
                implode('<br>', $errors),
                'Verplichte velden ontbreken',
                ['back_link' => true]
            );
        }
    }
}
add_action('save_post', 'validate_required_fields', 10, 3);