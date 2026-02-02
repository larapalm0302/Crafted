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
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Selecteer afbeelding',
                button: { text: 'Gebruik deze afbeelding' },
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
