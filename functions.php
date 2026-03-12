<?php
function register_programma_posttype()
{
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

function programma_add_meta_boxes()
{
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

function programma_meta_box_callback($post)
{
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
        <td><textarea name="description" id="description" rows="4"
                      style="width: 100%;"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label>Afbeelding</label></th>
        <td>
            <div id="programma-image-preview" style="margin-bottom: 10px;">
                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>"
                     style="max-width: 200px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <input type="hidden" name="programma_image" id="programma_image" value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button" id="upload-image-btn">Afbeelding uploaden</button>
            <button type="button" class="button" id="remove-image-btn"
                    style="<?php echo $image_id ? '' : 'display:none;'; ?>">Verwijderen</button>
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

function programma_save_meta($post_id)
{
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

function programma_set_required_notice($message)
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }
    set_transient('programma_required_notice_' . $user_id, $message, 60);
}

function programma_validate_required_fields($post_id, $post, $update)
{
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

function programma_render_required_notice()
{
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

function programma_enqueue_admin_scripts($hook)
{
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        global $post;
        if ($post && $post->post_type === 'programma') {
            wp_enqueue_media();
        }
    }
}
add_action('admin_enqueue_scripts', 'programma_enqueue_admin_scripts');

function get_field($field, $post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, $field, true);
}

function crafted_contact_settings_init()
{
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

function crafted_contact_email_field()
{
    $value = get_option('crafted_contact_email', 'crafted@summacollege.nl');
    echo '<input type="email" name="crafted_contact_email" value="' . esc_attr($value) . '" class="regular-text">';
}

function crafted_contact_phone_field()
{
    $value = get_option('crafted_contact_phone', 'Telefoonnummer hier');
    echo '<input type="text" name="crafted_contact_phone" value="' . esc_attr($value) . '" class="regular-text">';
}

function crafted_contact_address_field()
{
    $value = get_option('crafted_contact_address', 'Klokgebouw 50, 5617 AB Eindhoven');
    echo '<input type="text" name="crafted_contact_address" value="' . esc_attr($value) . '" class="regular-text">';
}

function crafted_contact_menu()
{
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

function crafted_contact_page()
{
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

function add_styles()
{
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Krona+One&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
}

add_action('wp_enqueue_scripts', 'add_styles');

// --- Enqueue Frontend Assets ---
function crafted_friends_enqueue_assets()
{
    wp_enqueue_style('crafted-main-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('crafted-footer-style', get_template_directory_uri() . '/assets/css/footer.css', [], '1.0');
    if (is_front_page() || is_page_template('page-home.php') || is_page('livestream') || is_page_template('page-livestream.php') || is_page('crafted-friends') || is_page_template('page-crafted-friends.php')) {
        wp_enqueue_style('crafted-livestream-style', get_template_directory_uri() . '/assets/css/style-blocks.css', [], '3.0');
    }
    wp_enqueue_script('crafted-carousel', get_template_directory_uri() . '/assets/js/carousel.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'crafted_friends_enqueue_assets');



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
            'menu_name' => 'Nieuws',
            'name_admin_bar' => 'Nieuwsbericht',
            'search_items' => 'Zoek nieuws',
            'not_found' => 'Geen nieuws gevonden',
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
        <td><textarea name="description" id="description" rows="4"
                      style="width: 100%;"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label>Afbeelding <span style="color:red;">*</span></label></th>
        <td>
            <div id="nieuws-image-preview" style="margin-bottom: 10px;">
                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>"
                     style="max-width: 200px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <input type="hidden" name="nieuws_image" id="nieuws_image" value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button" id="upload-image-btn">Afbeelding uploaden</button>
            <button type="button" class="button" id="remove-image-btn"
                    style="<?php echo $image_id ? '' : 'display:none;'; ?>">Verwijderen</button>
        </td>
    </tr>
    <tr>
        <th><label for="raw_nieuws_date">Datum & Tijd</label></th>
        <td><input type="datetime-local" name="raw_nieuws_date" id="raw_nieuws_date"
                   value="<?php echo esc_attr($rawdatetime); ?>"></td>
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
        return;
    }

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

function register_teasers_posttype()
{
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

function add_teasers_meta_boxes()
{
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

function teasers_meta_box_callback($post)
{
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
        <td><textarea name="description" id="description" rows="4"
                      style="width: 100%;"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label>Afbeelding <span style="color:red;">*</span></label></th>
        <td>
            <div id="teasers-image-preview" style="margin-bottom: 10px;">
                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>"
                     style="max-width: 200px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <input type="hidden" name="teasers_image" id="teasers_image" value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button" id="upload-image-btn">Afbeelding uploaden</button>
            <button type="button" class="button" id="remove-image-btn"
                    style="<?php echo $image_id ? '' : 'display:none;'; ?>">Verwijderen</button>
        </td>
    </tr>
    <tr>
        <th><label for="raw_teasers_date">Datum & Tijd</label></th>
        <td><input type="datetime-local" name="raw_teasers_date" id="raw_teasers_date"
                   value="<?php echo esc_attr($rawdatetime); ?>"></td>
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

function teasers_save_meta($post_id)
{
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


function register_storylines_posttype()
{
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

function add_storylines_meta_boxes()
{
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

function storylines_meta_box_callback($post)
{
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
        <td><textarea name="description" id="description" rows="4"
                      style="width: 100%;"><?php echo esc_textarea($description); ?></textarea></td>
    </tr>
    <tr>
        <th><label>Afbeelding <span style="color:red;">*</span></label></th>
        <td>
            <div id="storylines-image-preview" style="margin-bottom: 10px;">
                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>"
                     style="max-width: 200px; height: auto; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <input type="hidden" name="storylines_image" id="storylines_image"
                   value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button" id="upload-image-btn">Afbeelding uploaden</button>
            <button type="button" class="button" id="remove-image-btn"
                    style="<?php echo $image_id ? '' : 'display:none;'; ?>">Verwijderen</button>
        </td>
    </tr>
    <tr>
        <th><label for="raw_storylines_date">Datum & Tijd</label></th>
        <td><input type="datetime-local" name="raw_storylines_date" id="raw_storylines_date"
                   value="<?php echo esc_attr($rawdatetime); ?>"></td>
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

function storylines_save_meta($post_id)
{
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
                'public' => true,
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

function validate_required_fields($post_id, $post, $update)
{
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

// ============================================================
// KOPIEER ALLES HIERONDER NAAR HET EINDE VAN functions.php
// ============================================================

// --- Theme Setup ---
function crafted_theme_setup()
{
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'crafted_theme_setup');

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
    $extended_info = get_post_meta($post->ID, 'school_extended_info', true);

    // CSS Styling for the Admin Meta Box
    echo '<style>
        .crafted-admin-box { background: #f9f9f9; border-left: 4px solid #C25A95; padding: 15px 20px; margin-bottom: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .crafted-admin-box h4 { margin-top: 0; color: #773570; font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 8px; }
        .crafted-admin-field { margin-bottom: 15px; }
        .crafted-admin-field label { font-weight: 600; display: block; margin-bottom: 5px; color: #333; }
        .crafted-admin-field input.widefat { border: 1px solid #ccc; padding: 6px 10px; border-radius: 4px; }
        .crafted-admin-field .description { font-size: 12px; color: #666; font-style: italic; display: block; margin-top: 4px;}
    </style>';

    echo '<div style="padding-top: 10px;">';

    // SECTION 1: Text & Pop-up
    echo '<div class="crafted-admin-box">';
    echo '<h4>1. Tekst & Pop-up Inhoud</h4>';

    echo '<div class="crafted-admin-field">';
    echo '<label>School Subtitel:</label>';
    echo '<input type="text" name="school_subtitle" value="' . esc_attr($subtitle) . '" class="widefat" placeholder="Bijv. MBO College of Vakgebied">';
    echo '</div>';

    echo '<div class="crafted-admin-field" style="margin-top: 20px;">';
    echo '<label>Uitgebreide Info (Verschijnt in de Pop-up):</label>';
    echo '<span class="description">Als je hier tekst instelt, verandert de "Meer informatie" knop aan de voorkant in een pop-up venster.</span><br>';
    wp_editor($extended_info, 'school_extended_info', array(
        'media_buttons' => true,
        'textarea_rows' => 12,
        'teeny' => false
    ));
    echo '</div>';

    echo '</div>'; // End SECTION 1

    // SECTION 2: Links
    echo '<div class="crafted-admin-box">';
    echo '<h4>2. Externe Links (Website & Knoppen)</h4>';

    echo '<div class="crafted-admin-field">';
    echo '<label>Referentie Website Link (URL):</label>';
    echo '<input type="url" name="school_link" value="' . esc_attr($link) . '" class="widefat" placeholder="https://www.voorbeeldschool.nl">';
    echo '<span class="description">Als er géén pop-up tekst is, is dit de hoofdknop. Anders komt deze als extra knop ónderin de pop-up te staan.</span>';
    echo '</div>';
    echo '</div>';

    // SECTION 3: Afbeeldingen
    echo '<div class="crafted-admin-box" style="border-left-color: #773570;">';
    echo '<h4>3. Afbeeldingen & Icoontjes</h4>';

    echo '<div class="crafted-admin-field">';
    echo '<label>Icoon URL (klein logo over de foto in het raster):</label>';
    echo '<input type="text" name="school_icon_url" value="' . esc_attr($icon_url) . '" class="widefat" placeholder="https://...bestand.png">';
    echo '<span class="description">Optioneel: Plak hier een complete media URL voor het extra vignet/logo linksboven de school in het overzicht.</span>';
    echo '</div>';

    echo '<div class="crafted-admin-field" style="margin-top: 20px; padding-top: 10px; border-top: 1px dashed #ccc;">';
    crafted_render_image_field('school_image', $post->ID, 'School Hoofdafbeelding (Vervangt het standaard uitgelichte bestand)');
    echo '</div>';
    echo '</div>';

    echo '</div>'; // End wrapper
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

    // Reusing the CSS from School (just to be safe it always loads)
    echo '<style>
        .crafted-admin-box { background: #f9f9f9; border-left: 4px solid #C25A95; padding: 15px 20px; margin-bottom: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .crafted-admin-box h4 { margin-top: 0; color: #773570; font-size: 16px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 8px; }
        .crafted-admin-field { margin-bottom: 15px; }
        .crafted-admin-field label { font-weight: 600; display: block; margin-bottom: 5px; color: #333; }
        .crafted-admin-field input.widefat { border: 1px solid #ccc; padding: 6px 10px; border-radius: 4px; }
        .crafted-admin-field .description { font-size: 12px; color: #666; font-style: italic; display: block; margin-top: 4px;}
        .ls-live-badge { display:inline-block; background: #e00; color: #fff; border-radius: 3px; padding: 2px 6px; font-weight: bold; font-size: 11px; margin-left:10px; }
    </style>';

    echo '<div style="padding-top: 10px;">';

    // SECTION 1: Status & Setup
    echo '<div class="crafted-admin-box">';
    echo '<h4>1. Status & Video Bron</h4>';

    echo '<div class="crafted-admin-field" style="background:#fff; padding:10px; border:1px solid #ddd; border-radius:4px;">';
    echo '<label style="cursor:pointer;">';
    echo '<input type="checkbox" name="ls_is_live" value="1" ' . checked($is_live, '1', false) . '>';
    echo '<span style="font-size:14px; margin-left:5px;"><strong>Staat de stream momenteel LIVE?</strong></span>';
    echo '<span class="ls-live-badge">LIVE</span>';
    echo '</label>';
    echo '<span class="description" style="margin-left: 24px;">Vink dit aan om het knipperende rode LIVE icoontje en rode randen op de website te activeren.</span>';
    echo '</div>';

    echo '<div class="crafted-admin-field" style="margin-top:20px;">';
    echo '<label>YouTube Link (Video URL):</label>';
    echo '<input type="text" name="ls_video_url" value="' . esc_attr($video_url) . '" class="widefat" placeholder="https://youtube.com/watch?v=...">';
    echo '<span class="description">Plak hier de volledige YouTube link. Het systeem haalt de speler er automatisch uit.</span>';
    echo '</div>';
    echo '</div>'; // End SECTION 1

    // SECTION 2: Branding
    echo '<div class="crafted-admin-box" style="border-left-color: #773570;">';
    echo '<h4>2. Kanaal & Navigatie</h4>';

    echo '<div class="crafted-admin-field">';
    echo '<label>Kanaal Naam (Weergegeven onder de videotitel):</label>';
    echo '<input type="text" name="ls_channel_name" value="' . esc_attr($channel_name) . '" class="widefat" placeholder="Bijv. CRAFTED TV">';
    echo '</div>';

    echo '<div class="crafted-admin-field" style="margin-top:20px; padding-top:10px; border-top:1px dashed #ccc;">';
    crafted_render_image_field('ls_logo_url_id', $post->ID, 'Kanaal Logo (Wordt rond weergegeven naast de naam)');
    echo '</div>';

    echo '<div class="crafted-admin-field" style="margin-top:20px; padding-top:10px; border-top:1px dashed #ccc;">';
    echo '<label>Terugknop URL (Linksboven de livestream):</label>';
    echo '<input type="text" name="ls_back_url" value="' . esc_attr($back_url) . '" class="widefat" placeholder="/">';
    echo '<span class="description">Zet hier "/" neer om bezoekers naar de homepage terug te sturen, of vul een specifieke link in.</span>';
    echo '</div>';

    echo '</div>'; // End SECTION 2

    echo '</div>'; // End wrapper
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
        'school_subtitle',
        'school_link',
        'school_icon_url',
        'ambassadeur_quote',
        'ls_video_url',
        'ls_channel_name',
        'ls_back_url'
    ];
    foreach ($text_fields as $field) {
        if (isset($_POST[$field]))
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
    }

    // Specially handle the rich text editor for extended info since it has HTML
    if (isset($_POST['school_extended_info'])) {
        update_post_meta($post_id, 'school_extended_info', wp_kses_post($_POST['school_extended_info']));
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

// --- Home Menu Pagina ---
function crafted_home_menu()
{
    add_menu_page('Home Opties', 'Home Opties', 'manage_options', 'crafted-home', 'crafted_home_page', 'dashicons-admin-home', 30);
}
add_action('admin_menu', 'crafted_home_menu');

function crafted_home_page()
{
    wp_enqueue_media();
    echo '<div class="wrap"><h1>Home Pagina Instellingen</h1><form method="post" action="options.php">';
    settings_fields('crafted_home_group');
    do_settings_sections('crafted_home');
    submit_button();
    echo '</form></div>';
    ?>
<script>
jQuery(document).ready(function($) {
    // Card image upload buttons
    $('.crafted-card-upload-btn').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var fieldId = btn.data('field');
        var frame = wp.media({
            title: 'Selecteer kaart afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#' + fieldId).val(attachment.id);
            var imgUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
            $('#' + fieldId + '_preview').html('<img src="' + imgUrl + '" style="max-width:200px;height:auto;border-radius:8px;">');
            btn.siblings('.crafted-card-remove-btn').show();
        });
        frame.open();
    });
    $('.crafted-card-remove-btn').on('click', function(e) {
        e.preventDefault();
        var fieldId = $(this).data('field');
        $('#' + fieldId).val('');
        $('#' + fieldId + '_preview').html('');
        $(this).hide();
    });
});
</script>
<?php
}

// --- Home Settings ---
function crafted_home_settings_init()
{
    // Hero Sectie Background
    add_settings_section('crafted_home_hero_section', 'Hero Achtergrond (Video of Afbeelding)', '__return_false', 'crafted_home');

    // Video URL (has priority over image if filled)
    register_setting('crafted_home_group', 'crafted_home_video_url');
    add_settings_field('crafted_home_video_url', 'Achtergrond Video URL (.mp4)', function () {
        $val = get_option('crafted_home_video_url');
        echo '<input type="url" name="crafted_home_video_url" value="' . esc_attr($val) . '" class="regular-text" placeholder="https://.../video.mp4" style="width: 100%; max-width: 600px;">';
        echo '<p class="description">Optioneel. Plak hier een directe link naar een .mp4 video. (Laat leeg om de afbeelding hieronder te gebruiken)</p>';
    }, 'crafted_home', 'crafted_home_hero_section');

    // Image Upload
    register_setting('crafted_home_group', 'crafted_home_image_id');
    add_settings_field('crafted_home_image_id', 'Fallback Achtergrond Afbeelding', function () {
        $image_id = get_option('crafted_home_image_id');
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        ?>
<div class="crafted-home-image-preview" style="margin-bottom:10px;">
    <img src="<?php echo esc_url($image_url); ?>"
         style="max-width:300px; display:<?php echo $image_id ? 'block' : 'none'; ?>; border:1px solid #ccc; padding:2px;" />
</div>
<input type="hidden" name="crafted_home_image_id" id="crafted_home_image_id"
       value="<?php echo esc_attr($image_id); ?>" />
<button type="button" class="button" id="crafted_home_image_upload_btn">Afbeelding Selecteren</button>
<button type="button" class="button button-link-delete" id="crafted_home_image_remove_btn"
        style="display:<?php echo $image_id ? 'inline-block' : 'none'; ?>; color: #a00;">Verwijderen</button>

<script>
jQuery(document).ready(function($) {
    var mediaUploader;
    $('#crafted_home_image_upload_btn').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media({
            title: 'Kies Achtergrond Afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#crafted_home_image_id').val(attachment.id);
            $('.crafted-home-image-preview img').attr('src', attachment.url).show();
            $('#crafted_home_image_remove_btn').show();
        });
        mediaUploader.open();
    });
    $('#crafted_home_image_remove_btn').click(function(e) {
        e.preventDefault();
        $('#crafted_home_image_id').val('');
        $('.crafted-home-image-preview img').hide().attr('src', '');
        $(this).hide();
    });
});
</script>
<?php
    }, 'crafted_home', 'crafted_home_hero_section');

    // Info Balk Caroussel
    add_settings_section('crafted_home_carousel_section', 'Info Balk Caroussel', '__return_false', 'crafted_home');

    for ($i = 1; $i <= 3; $i++) {
        register_setting('crafted_home_group', "crafted_home_carousel_{$i}");
        add_settings_field("crafted_home_carousel_{$i}", "Caroussel Tekst $i", function () use ($i) {
            $val = get_option("crafted_home_carousel_{$i}");
            echo '<input type="text" name="crafted_home_carousel_' . $i . '" value="' . esc_attr($val) . '" class="regular-text">';
        }, 'crafted_home', 'crafted_home_carousel_section');
    }

    // =============================================
    // WHAT AWAITS YOU CARDS (3 cards)
    // =============================================
    add_settings_section('crafted_home_cards_section', '🎴 What Awaits You Kaarten', function () {
        echo '<p>Beheer de 3 kaarten in de "What Awaits You" sectie. Elk kaart heeft een titel, beschrijving, afbeelding en knop-link.</p>';
    }, 'crafted_home');

    $card_defaults = [
        1 => ['title' => 'Programma', 'desc' => 'State-of-the-art lighting, visuals, and production design create a multi-sensory journey like no other'],
        2 => ['title' => 'Tickets', 'desc' => 'World-class artists and emerging talents come together on multiple stages to create unforgettable moments'],
        3 => ['title' => 'Crafted & Friends', 'desc' => 'Dance until sunrise with cutting-edge electronic music from renowned DJs and producers from around the globe'],
    ];

    for ($i = 1; $i <= 3; $i++) {
        $defaults = $card_defaults[$i];

        // Title
        register_setting('crafted_home_group', "crafted_home_card_{$i}_title");
        add_settings_field("crafted_home_card_{$i}_title", "Kaart $i — Titel", function () use ($i, $defaults) {
            $val = get_option("crafted_home_card_{$i}_title", $defaults['title']);
            echo '<input type="text" name="crafted_home_card_' . $i . '_title" value="' . esc_attr($val) . '" class="regular-text" style="width:100%;max-width:400px;">';
        }, 'crafted_home', 'crafted_home_cards_section');

        // Description
        register_setting('crafted_home_group', "crafted_home_card_{$i}_desc");
        add_settings_field("crafted_home_card_{$i}_desc", "Kaart $i — Beschrijving", function () use ($i, $defaults) {
            $val = get_option("crafted_home_card_{$i}_desc", $defaults['desc']);
            echo '<textarea name="crafted_home_card_' . $i . '_desc" rows="3" style="width:100%;max-width:500px;">' . esc_textarea($val) . '</textarea>';
        }, 'crafted_home', 'crafted_home_cards_section');

        // Image
        register_setting('crafted_home_group', "crafted_home_card_{$i}_image");
        add_settings_field("crafted_home_card_{$i}_image", "Kaart $i — Afbeelding", function () use ($i) {
            $image_id = get_option("crafted_home_card_{$i}_image");
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
            $field_id = "crafted_home_card_{$i}_image";
            ?>
<div id="<?= $field_id ?>_preview" style="margin-bottom:10px;">
    <?php if ($image_url): ?><img src="<?= esc_url($image_url) ?>"
         style="max-width:200px;height:auto;border-radius:8px;"><?php endif; ?>
</div>
<input type="hidden" name="<?= $field_id ?>" id="<?= $field_id ?>" value="<?= esc_attr($image_id) ?>">
<button type="button" class="button crafted-card-upload-btn" data-field="<?= $field_id ?>">Afbeelding
    Selecteren</button>
<button type="button" class="button button-link-delete crafted-card-remove-btn" data-field="<?= $field_id ?>"
        style="<?= $image_id ? '' : 'display:none;' ?>color:#a00;">Verwijderen</button>
<?php
        }, 'crafted_home', 'crafted_home_cards_section');

        // Button Link
        register_setting('crafted_home_group', "crafted_home_card_{$i}_link");
        add_settings_field("crafted_home_card_{$i}_link", "Kaart $i — Knop Link", function () use ($i) {
            $val = get_option("crafted_home_card_{$i}_link", '#');
            echo '<input type="text" name="crafted_home_card_' . $i . '_link" value="' . esc_attr($val) . '" class="regular-text" placeholder="/programma" style="width:100%;max-width:400px;">';
            if ($i < 3)
                echo '<hr style="margin-top:20px;border-color:#ddd;">';
        }, 'crafted_home', 'crafted_home_cards_section');
    }

    // =============================================
    // LOCATIE SETTINGS
    // =============================================
    add_settings_section('crafted_home_locatie_section', '📍 Locatie Instellingen', function () {
        echo '<p>Beheer het adres, de Google Maps kaart en de routeknop.</p>';
    }, 'crafted_home');

    register_setting('crafted_home_group', 'crafted_home_locatie_adres_titel');
    add_settings_field('crafted_home_locatie_adres_titel', 'Adres Regel 1', function () {
        $val = get_option('crafted_home_locatie_adres_titel', 'Klokgebouw 50');
        echo '<input type="text" name="crafted_home_locatie_adres_titel" value="' . esc_attr($val) . '" class="regular-text">';
    }, 'crafted_home', 'crafted_home_locatie_section');

    register_setting('crafted_home_group', 'crafted_home_locatie_adres_tekst');
    add_settings_field('crafted_home_locatie_adres_tekst', 'Adres Regel 2', function () {
        $val = get_option('crafted_home_locatie_adres_tekst', '5617 AB Eindhoven');
        echo '<input type="text" name="crafted_home_locatie_adres_tekst" value="' . esc_attr($val) . '" class="regular-text">';
    }, 'crafted_home', 'crafted_home_locatie_section');

    register_setting('crafted_home_group', 'crafted_home_locatie_maps_url');
    add_settings_field('crafted_home_locatie_maps_url', 'Google Maps Embed URL', function () {
        $val = get_option('crafted_home_locatie_maps_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2250.1712848719576!2d5.454405376123775!3d51.44860461499746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c6d96b3a220f6b%3A0x3a0eb3741c513904!2sKlokgebouw%2C%20Eindhoven!5e1!3m2!1snl!2snl!4v1769778877889!5m2!1snl!2snl');
        echo '<input type="url" name="crafted_home_locatie_maps_url" value="' . esc_attr($val) . '" style="width:100%;max-width:600px;">';
        echo '<p class="description">Plak hier de volledige Google Maps embed URL (de src van de iframe).</p>';
    }, 'crafted_home', 'crafted_home_locatie_section');

    register_setting('crafted_home_group', 'crafted_home_locatie_route_url');
    add_settings_field('crafted_home_locatie_route_url', 'Routebeschrijving Link', function () {
        $val = get_option('crafted_home_locatie_route_url', '#');
        echo '<input type="url" name="crafted_home_locatie_route_url" value="' . esc_attr($val) . '" class="regular-text" placeholder="https://maps.google.com/...">';
    }, 'crafted_home', 'crafted_home_locatie_section');

    // =============================================
    // IN DE BUURT WIDGETS (max 8)
    // =============================================
    add_settings_section('crafted_home_buurt_section', '🏘️ In de buurt (max 8)', function () {
        echo '<p>Voeg tot 8 "In de buurt" items toe. Alleen items met een ingevulde titel worden getoond op de website.</p>';
    }, 'crafted_home');

    $buurt_defaults = [
        1 => ['title' => 'Biergarten Eindhoven', 'sub' => 'Bar & terras', 'dist' => '300m', 'icon' => 'bar'],
        2 => ['title' => 'Ketelhuis Strijp-S', 'sub' => 'Bar', 'dist' => '300m', 'icon' => 'bar'],
        3 => ['title' => "STR'EAT Bars & kitchens", 'sub' => 'Restaurant', 'dist' => '300m', 'icon' => 'restaurant'],
        4 => ['title' => 'Hotel Crown', 'sub' => 'Comfortabel overnachten', 'dist' => '800m', 'icon' => 'hotel'],
        5 => ['title' => 'NS Station Eindhoven', 'sub' => 'Trein & bus verbindingen', 'dist' => '1.2km', 'icon' => 'station'],
        6 => ['title' => 'Parkeergarage P1', 'sub' => '24/7 beschikbaar', 'dist' => '150m', 'icon' => 'parkeren'],
        7 => ['title' => '', 'sub' => '', 'dist' => '', 'icon' => 'overig'],
        8 => ['title' => '', 'sub' => '', 'dist' => '', 'icon' => 'overig'],
    ];

    $icon_options = [
        'bar' => '🍺 Bar',
        'restaurant' => '🍴 Restaurant',
        'hotel' => '🏨 Hotel',
        'station' => '🚉 Station / OV',
        'parkeren' => '🅿️ Parkeren',
        'overig' => '📌 Overig',
    ];

    for ($i = 1; $i <= 8; $i++) {
        $def = $buurt_defaults[$i];

        register_setting('crafted_home_group', "crafted_home_buurt_{$i}_title");
        register_setting('crafted_home_group', "crafted_home_buurt_{$i}_sub");
        register_setting('crafted_home_group', "crafted_home_buurt_{$i}_dist");
        register_setting('crafted_home_group', "crafted_home_buurt_{$i}_icon");

        add_settings_field("crafted_home_buurt_{$i}", "Item $i", function () use ($i, $def, $icon_options) {
            $title = get_option("crafted_home_buurt_{$i}_title", $def['title']);
            $sub = get_option("crafted_home_buurt_{$i}_sub", $def['sub']);
            $dist = get_option("crafted_home_buurt_{$i}_dist", $def['dist']);
            $icon = get_option("crafted_home_buurt_{$i}_icon", $def['icon']);

            echo '<div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">';
            echo '<input type="text" name="crafted_home_buurt_' . $i . '_title" value="' . esc_attr($title) . '" placeholder="Titel" style="width:180px;">';
            echo '<input type="text" name="crafted_home_buurt_' . $i . '_sub" value="' . esc_attr($sub) . '" placeholder="Subtitel" style="width:180px;">';
            echo '<input type="text" name="crafted_home_buurt_' . $i . '_dist" value="' . esc_attr($dist) . '" placeholder="Afstand" style="width:80px;">';
            echo '<select name="crafted_home_buurt_' . $i . '_icon">';
            foreach ($icon_options as $key => $label) {
                $selected = ($icon === $key) ? ' selected' : '';
                echo '<option value="' . $key . '"' . $selected . '>' . $label . '</option>';
            }
            echo '</select>';
            echo '</div>';
        }, 'crafted_home', 'crafted_home_buurt_section');
    }

    // =============================================
    // NIEUWS SECTIE
    // =============================================
    // --- Plattegrond Sectie ---
    add_settings_section('crafted_home_plattegrond_section', '4. Plattegrond Sectie', function () {
        echo '<p>Upload hier de plattegrond afbeelding. De titel en tekst zijn optioneel. Als je geen tekst invult, wordt de plattegrond gecentreerd over de volle breedte getoond.</p>';
    }, 'crafted_home');

    register_setting('crafted_home_group', 'crafted_home_plattegrond_titel');
    register_setting('crafted_home_group', 'crafted_home_plattegrond_tekst');
    register_setting('crafted_home_group', 'crafted_home_plattegrond_img');

    add_settings_field('crafted_home_plattegrond_titel', 'Titel', function () {
        $val = get_option('crafted_home_plattegrond_titel', 'Plattegrond');
        echo '<input type="text" name="crafted_home_plattegrond_titel" value="' . esc_attr($val) . '" class="regular-text">';
    }, 'crafted_home', 'crafted_home_plattegrond_section');

    add_settings_field('crafted_home_plattegrond_tekst', 'Beschrijving', function () {
        $val = get_option('crafted_home_plattegrond_tekst', '');
        echo '<textarea name="crafted_home_plattegrond_tekst" rows="4" class="large-text" style="width:100%">' . esc_textarea($val) . '</textarea>';
    }, 'crafted_home', 'crafted_home_plattegrond_section');

    add_settings_field('crafted_home_plattegrond_img', 'Plattegrond Afbeelding', function () {
        $img_id = get_option('crafted_home_plattegrond_img');
        $img_src = '';
        if ($img_id) {
            $img_src = wp_get_attachment_image_url($img_id, 'large');
        }
        echo '<div style="margin-bottom:10px;">';
        if ($img_src) {
            echo '<img id="crafted_home_plattegrond_img_preview" src="' . esc_url($img_src) . '" style="max-width:300px; height:auto; display:block; border:1px solid #ccc; padding:5px; background:#fff;">';
        } else {
            echo '<img id="crafted_home_plattegrond_img_preview" src="" style="max-width:300px; height:auto; display:none; border:1px solid #ccc; padding:5px; background:#fff;">';
        }
        echo '</div>';
        echo '<input type="hidden" id="crafted_home_plattegrond_img_id" name="crafted_home_plattegrond_img" value="' . esc_attr($img_id) . '">';
        echo '<button type="button" class="button crafted-upload-btn" data-target="crafted_home_plattegrond_img">Selecteer Afbeelding</button>';
        echo '<button type="button" class="button crafted-remove-btn" data-target="crafted_home_plattegrond_img" style="margin-left:5px;">Verwijder</button>';
    }, 'crafted_home', 'crafted_home_plattegrond_section');

    add_settings_section('crafted_home_nieuws_section', '📰 Nieuws Sectie', function () {
        echo '<p>Beheer de "Volg hier het laatste nieuws" sectie op de homepage.</p>';
    }, 'crafted_home');

    register_setting('crafted_home_group', 'crafted_home_nieuws_title');
    add_settings_field('crafted_home_nieuws_title', 'Titel', function () {
        $val = get_option('crafted_home_nieuws_title', 'Volg hier het laatste nieuws!');
        echo '<input type="text" name="crafted_home_nieuws_title" value="' . esc_attr($val) . '" class="regular-text" style="width:100%;max-width:400px;">';
    }, 'crafted_home', 'crafted_home_nieuws_section');

    register_setting('crafted_home_group', 'crafted_home_nieuws_desc');
    add_settings_field('crafted_home_nieuws_desc', 'Beschrijving', function () {
        $val = get_option('crafted_home_nieuws_desc', 'Blijf op de hoogte met teasers, previews en andere interessante updates.');
        echo '<textarea name="crafted_home_nieuws_desc" rows="2" style="width:100%;max-width:500px;">' . esc_textarea($val) . '</textarea>';
    }, 'crafted_home', 'crafted_home_nieuws_section');

    register_setting('crafted_home_group', 'crafted_home_nieuws_link');
    add_settings_field('crafted_home_nieuws_link', 'Knop Link', function () {
        $val = get_option('crafted_home_nieuws_link', '/nieuws');
        echo '<input type="text" name="crafted_home_nieuws_link" value="' . esc_attr($val) . '" class="regular-text" placeholder="/nieuws">';
    }, 'crafted_home', 'crafted_home_nieuws_section');

    register_setting('crafted_home_group', 'crafted_home_nieuws_image');
    add_settings_field('crafted_home_nieuws_image', 'Afbeelding', function () {
        $image_id = get_option('crafted_home_nieuws_image');
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        $fid = 'crafted_home_nieuws_image';
        echo '<div id="' . $fid . '_preview" style="margin-bottom:10px;">';
        if ($image_url)
            echo '<img src="' . esc_url($image_url) . '" style="max-width:200px;height:auto;border-radius:8px;">';
        echo '</div>';
        echo '<input type="hidden" name="' . $fid . '" id="' . $fid . '" value="' . esc_attr($image_id) . '">';
        echo '<button type="button" class="button crafted-card-upload-btn" data-field="' . $fid . '">Afbeelding Selecteren</button>';
        echo '<button type="button" class="button button-link-delete crafted-card-remove-btn" data-field="' . $fid . '" style="' . ($image_id ? '' : 'display:none;') . 'color:#a00;">Verwijderen</button>';
    }, 'crafted_home', 'crafted_home_nieuws_section');

    register_setting('crafted_home_group', 'crafted_home_nieuws_credit');
    add_settings_field('crafted_home_nieuws_credit', 'Credit Tekst', function () {
        $val = get_option('crafted_home_nieuws_credit', 'Door Summa Marketing');
        echo '<input type="text" name="crafted_home_nieuws_credit" value="' . esc_attr($val) . '" class="regular-text" placeholder="Door ...">';
    }, 'crafted_home', 'crafted_home_nieuws_section');
}
add_action('admin_init', 'crafted_home_settings_init');

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
        register_setting('crafted_footer_group', "crafted_footer_btn_{$i}_icon");
        add_settings_field(
            "crafted_footer_btn_{$i}",
            "Button $i (Text & URL)",
            function () use ($i) {
                crafted_footer_btn_cb($i);
            },
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
function crafted_social_insta_cb()
{
    echo '<input type="text" name="crafted_social_insta" value="' . esc_attr(get_option('crafted_social_insta')) . '" class="regular-text" placeholder="https://instagram.com/...">';
}
function crafted_social_linkedin_cb()
{
    echo '<input type="text" name="crafted_social_linkedin" value="' . esc_attr(get_option('crafted_social_linkedin')) . '" class="regular-text" placeholder="https://linkedin.com/...">';
}
function crafted_social_tiktok_cb()
{
    echo '<input type="text" name="crafted_social_tiktok" value="' . esc_attr(get_option('crafted_social_tiktok')) . '" class="regular-text" placeholder="https://tiktok.com/...">';
}
function crafted_social_youtube_cb()
{
    echo '<input type="text" name="crafted_social_youtube" value="' . esc_attr(get_option('crafted_social_youtube')) . '" class="regular-text" placeholder="https://youtube.com/...">';
}
function crafted_footer_btn_cb($i)
{
    $text = get_option("crafted_footer_btn_{$i}_text", "Knop $i");
    $url = get_option("crafted_footer_btn_{$i}_url", "#");
    $icon = get_option("crafted_footer_btn_{$i}_icon", "");

    // Predefined list of useful Dashicons
    $available_icons = [
        '' => 'Standaard Icoon',
        'dashicons-star-filled' => 'Ster',
        'dashicons-heart' => 'Hartje',
        'dashicons-location' => 'Locatie Pin',
        'dashicons-calendar-alt' => 'Kalender',
        'dashicons-tickets-alt' => 'Tickets',
        'dashicons-groups' => 'Groep / Mensen',
        'dashicons-format-audio' => 'Muziek / Audio',
        'dashicons-camera' => 'Camera',
        'dashicons-megaphone' => 'Megafoon',
        'dashicons-email' => 'E-mail',
        'dashicons-info' => 'Informatie (i)',
        'dashicons-editor-help' => 'Vraagteken (?)'
    ];

    echo '<input type="text" name="crafted_footer_btn_' . $i . '_text" value="' . esc_attr($text) . '" placeholder="Tekst" style="margin-right:10px; width: 120px;">';
    echo '<input type="text" name="crafted_footer_btn_' . $i . '_url" value="' . esc_attr($url) . '" placeholder="URL" class="regular-text" style="width: 200px; margin-right:10px;">';

    // Dropdown for icons
    echo '<select name="crafted_footer_btn_' . $i . '_icon" style="width: 150px;">';
    foreach ($available_icons as $class => $label) {
        $selected = selected($icon, $class, false);
        echo '<option value="' . esc_attr($class) . '" ' . $selected . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}


// =============================================
// MENU OPTIES
// =============================================
function crafted_menu_menu()
{
    add_menu_page('Menu Opties', 'Menu Opties', 'manage_options', 'crafted-menu', 'crafted_menu_page', 'dashicons-menu', 32);
}
add_action('admin_menu', 'crafted_menu_menu');

function crafted_menu_page()
{
    echo '<div class="wrap"><h1>Menu Instellingen</h1><form method="post" action="options.php">';
    settings_fields('crafted_menu_group');
    do_settings_sections('crafted_menu');
    submit_button();
    echo '</form></div>';
}

function crafted_menu_settings_init()
{
    // Info blok tekst
    add_settings_section('crafted_menu_info_section', '1. Info Blok Tekst (Paarse vlak)', '__return_false', 'crafted_menu');

    register_setting('crafted_menu_group', 'crafted_menu_info_text');
    add_settings_field('crafted_menu_info_text', 'Info Tekst', function () {
        $val = get_option('crafted_menu_info_text', 'Lorem ipsum dolor sit amet, .consectetur adipiscing elit. In nibh libero, feugiat ac laoreet ac, dictum mollis turpis. Donec euismod sapien non nisl porta, at dapibus mauris laoreet. Duis in tincidunt justo, rhoncus ultrices libero. Proin sodales suscipit ex eget consequat. Pellentesque at orci efficitur, molestie risus in, porta diam. Phasellus ac efficitur metus, a fermentum leo. Maecenas posuere luctus urna et vehicula. Vestibulum et quam in orci cursus interdum non at leo. Nunc non ante ultricies, viverra dui eu, interdum elit.');
        echo '<textarea name="crafted_menu_info_text" rows="6" style="width:100%;max-width:800px;">' . esc_textarea($val) . '</textarea>';
        echo '<p class="description">Deze tekst wordt getoond in het paarse info-blokje in het menu overlay.</p>';
    }, 'crafted_menu', 'crafted_menu_info_section');

    // Knoppen
    add_settings_section('crafted_menu_buttons_section', '2. Menu Knoppen Links (6 stuks)', function () {
        echo '<p>Beheer de links en de (vertaalde) namen van de 6 grote knoppen in het menu overlay.</p>';
    }, 'crafted_menu');

    $btn_defaults = [
        1 => ['nl' => 'Home', 'en' => 'Home', 'url' => '/'],
        2 => ['nl' => 'Nieuws', 'en' => 'News', 'url' => '/nieuws'],
        3 => ['nl' => 'Contact', 'en' => 'Contact', 'url' => '/contact'],
        4 => ['nl' => 'Crafted<span class="smallFont">&</span>Friends', 'en' => '', 'url' => '/crafted-friends'],
        5 => ['nl' => 'Tickets', 'en' => 'Tickets', 'url' => '/tickets'],
        6 => ['nl' => 'Programma', 'en' => 'Program', 'url' => '/programma'],
    ];

    for ($i = 1; $i <= 6; $i++) {
        $def = $btn_defaults[$i];

        register_setting('crafted_menu_group', "crafted_menu_btn_{$i}_nl");
        register_setting('crafted_menu_group', "crafted_menu_btn_{$i}_en");
        register_setting('crafted_menu_group', "crafted_menu_btn_{$i}_url");

        add_settings_field("crafted_menu_btn_{$i}", "Knop $i", function () use ($i, $def) {
            $nl = get_option("crafted_menu_btn_{$i}_nl", $def['nl']);
            $en = get_option("crafted_menu_btn_{$i}_en", $def['en']);
            $url = get_option("crafted_menu_btn_{$i}_url", $def['url']);

            echo '<div style="display:flex; gap:10px; align-items:center;">';
            echo '<input type="text" name="crafted_menu_btn_' . $i . '_nl" value="' . esc_attr($nl) . '" placeholder="NL Tekst (HTML toegestaan)" style="width:220px;">';
            echo '<input type="text" name="crafted_menu_btn_' . $i . '_en" value="' . esc_attr($en) . '" placeholder="EN Tekst" style="width:150px;">';
            echo '<input type="text" name="crafted_menu_btn_' . $i . '_url" value="' . esc_attr($url) . '" placeholder="URL / Link" class="regular-text" style="width:250px;">';
            echo '</div>';
            if ($i == 4) {
                echo '<p class="description" style="margin-top:5px; margin-bottom: 20px;">Tip knop 4: Gebruik <code>Crafted&lt;br&gt;&lt;span class=&quot;smallFont&quot;&gt;&amp;&lt;/span&gt;&lt;br&gt;Friends</code> voor de speciale opmaak.</p>';
            }
        }, 'crafted_menu', 'crafted_menu_buttons_section');
    }

    // Timer Instellingen
    add_settings_section('crafted_menu_timer_section', '3. Countdown Timer', function () {
        echo '<p>Stel hier de doeldatum en tijd (formaat: <code>YYYY-MM-DDTHH:MM:SS</code>) van de countdown in, en wat de timer moet tonen als deze is afgelopen.</p>';
    }, 'crafted_menu');

    register_setting('crafted_menu_group', 'crafted_timer_date');
    register_setting('crafted_menu_group', 'crafted_timer_expired_text');
    register_setting('crafted_menu_group', 'crafted_timer_expired_url');

    add_settings_field('crafted_timer_date', 'Doeldatum & Tijd', function () {
        $val = get_option('crafted_timer_date', '2026-06-18T00:00:00');
        echo '<input type="datetime-local" name="crafted_timer_date" value="' . esc_attr($val) . '" class="regular-text">';
        echo '<p class="description">De datum en tijd waar de timer naartoe aftelt.</p>';
    }, 'crafted_menu', 'crafted_menu_timer_section');

    add_settings_field('crafted_timer_expired_text', 'Knop Tekst (Na afloop)', function () {
        $val = get_option('crafted_timer_expired_text', 'Kijk de livestream');
        echo '<input type="text" name="crafted_timer_expired_text" value="' . esc_attr($val) . '" class="regular-text">';
        echo '<p class="description">De tekst die in de timer verschijnt als deze op 0 staat.</p>';
    }, 'crafted_menu', 'crafted_menu_timer_section');

    add_settings_field('crafted_timer_expired_url', 'Knop URL (Na afloop)', function () {
        $val = get_option('crafted_timer_expired_url', '/livestream');
        echo '<input type="text" name="crafted_timer_expired_url" value="' . esc_attr($val) . '" class="regular-text">';
        echo '<p class="description">De link (URL) voor de livestream knop.</p>';
    }, 'crafted_menu', 'crafted_menu_timer_section');

}
add_action('admin_init', 'crafted_menu_settings_init');

function crafted_plattegrond_admin_scripts()
{
    ?>
<script>
jQuery(document).ready(function($) {
    // Plattegrond Uploader
    $('.crafted-upload-btn').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var target_id = button.data('target');

        var custom_uploader = wp.media({
            title: 'Kies een Afbeelding',
            button: {
                text: 'Gebruik deze Afbeelding'
            },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#' + target_id + '_id').val(attachment.id);
            $('#' + target_id + '_preview').attr('src', attachment.url).show();
        }).open();
    });

    // Verwijder knop
    $('.crafted-remove-btn').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var target_id = button.data('target');
        $('#' + target_id + '_id').val('');
        $('#' + target_id + '_preview').attr('src', '').hide();
    });
});
</script>
<?php
}
add_action('admin_footer', 'crafted_plattegrond_admin_scripts');

// =============================================
// COMING SOON OPTIES
// =============================================
function crafted_coming_soon_menu()
{
    add_menu_page('Coming Soon', 'Coming Soon', 'manage_options', 'crafted-coming-soon', 'crafted_coming_soon_page', 'dashicons-clock', 33);
}
add_action('admin_menu', 'crafted_coming_soon_menu');

function crafted_coming_soon_page()
{
    echo '<div class="wrap"><h1>Coming Soon Instellingen</h1><form method="post" action="options.php">';
    settings_fields('crafted_cs_group');
    do_settings_sections('crafted_cs');
    submit_button();
    echo '</form></div>';
}

function crafted_cs_settings_init()
{
    add_settings_section('crafted_cs_timer_section', 'Countdown Timer', function () {
        echo '<p>Stel hier de doeldatum en tijd in voor de <strong>Coming Soon pagina</strong>. Deze is onafhankelijk van de header timer.</p>';
    }, 'crafted_cs');

    register_setting('crafted_cs_group', 'crafted_cs_timer_date');
    register_setting('crafted_cs_group', 'crafted_cs_expired_text');
    register_setting('crafted_cs_group', 'crafted_cs_expired_url');

    add_settings_field('crafted_cs_timer_date', 'Doeldatum & Tijd', function () {
        $val = get_option('crafted_cs_timer_date', '2026-06-18T00:00:00');
        echo '<input type="datetime-local" name="crafted_cs_timer_date" value="' . esc_attr($val) . '" class="regular-text">';
    }, 'crafted_cs', 'crafted_cs_timer_section');

    add_settings_field('crafted_cs_expired_text', 'Knop Tekst (Na afloop)', function () {
        $val = get_option('crafted_cs_expired_text', 'Kijk de livestream');
        echo '<input type="text" name="crafted_cs_expired_text" value="' . esc_attr($val) . '" class="regular-text">';
    }, 'crafted_cs', 'crafted_cs_timer_section');

    add_settings_field('crafted_cs_expired_url', 'Knop URL (Na afloop)', function () {
        $val = get_option('crafted_cs_expired_url', '/livestream');
        echo '<input type="text" name="crafted_cs_expired_url" value="' . esc_attr($val) . '" class="regular-text">';
    }, 'crafted_cs', 'crafted_cs_timer_section');

    add_settings_section('crafted_cs_text_section', 'Teksten & Vertalingen', function () {
        echo '<p>Stel hier de teksten in voor de Coming Soon pagina in het Nederlands en Engels.</p>';
    }, 'crafted_cs');

    register_setting('crafted_cs_group', 'crafted_cs_title_nl');
    register_setting('crafted_cs_group', 'crafted_cs_title_en');
    register_setting('crafted_cs_group', 'crafted_cs_sub_nl');
    register_setting('crafted_cs_group', 'crafted_cs_sub_en');

    add_settings_field('crafted_cs_title', 'Titel (Groot)', function () {
        $nl = get_option('crafted_cs_title_nl', 'Snel online');
        $en = get_option('crafted_cs_title_en', 'Coming Soon');
        echo '<div><label style="display:inline-block;width:100px;">Nederlands:</label><input type="text" name="crafted_cs_title_nl" value="' . esc_attr($nl) . '" class="regular-text"></div>';
        echo '<div style="margin-top:10px;"><label style="display:inline-block;width:100px;">Engels:</label><input type="text" name="crafted_cs_title_en" value="' . esc_attr($en) . '" class="regular-text"></div>';
    }, 'crafted_cs', 'crafted_cs_text_section');

    add_settings_field('crafted_cs_sub', 'Ondertitel', function () {
        $nl = get_option('crafted_cs_sub_nl', 'We zijn nog even bezig met bouwen. De website lanceert over:');
        $en = get_option('crafted_cs_sub_en', 'We are currently building the site. We will launch in:');
        echo '<div><label style="display:inline-block;width:100px;">Nederlands:</label><textarea name="crafted_cs_sub_nl" rows="2" class="regular-text" style="width:300px;">' . esc_textarea($nl) . '</textarea></div>';
        echo '<div style="margin-top:10px;"><label style="display:inline-block;width:100px;">Engels:</label><textarea name="crafted_cs_sub_en" rows="2" class="regular-text" style="width:300px;">' . esc_textarea($en) . '</textarea></div>';
    }, 'crafted_cs', 'crafted_cs_text_section');
}
add_action('admin_init', 'crafted_cs_settings_init');