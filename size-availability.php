<?php
/**
 * Plugin Name: Product Size & Color Availability
 * Description: Manage product sizes and colors with picker, hex and RGB support.
 * Version: 1.1.0
 * Author: KiruthickRaj
 */

defined( 'ABSPATH' ) || exit;

/* =========================================
 * ADMIN: PRODUCT SIZES
 * ========================================= */
add_action( 'woocommerce_product_options_general_product_data', function () {

    global $post;

    $sizes = [
        's'   => 'S',
        'm'   => 'M',
        'l'   => 'L',
        'xl'  => 'XL',
        'xxl' => 'XXL',
    ];

    $saved = get_post_meta( $post->ID, '_custom_sizes', true );
    if ( ! is_array( $saved ) ) {
        $saved = [];
    }

    echo '<div class="options_group">';
    echo '<p><strong>Available Sizes</strong></p>';

    foreach ( $sizes as $key => $label ) {
        ?>
        <p class="form-field">
            <label>
                <input type="checkbox"
                       name="_custom_sizes[]"
                       value="<?php echo esc_attr( $key ); ?>"
                       <?php checked( in_array( $key, $saved, true ) ); ?> />
                <?php echo esc_html( $label ); ?>
            </label>
        </p>
        <?php
    }

    echo '</div>';
});

/* =========================================
 * ADMIN: PRODUCT COLORS (HEX + RGB + PICKER)
 * ========================================= */
add_action( 'woocommerce_product_options_general_product_data', function () {

    global $post;

    $colors = get_post_meta( $post->ID, '_custom_colors', true );
    if ( ! is_array( $colors ) ) {
        $colors = [];
    }

    echo '<div class="options_group">';
    echo '<p><strong>Product Colors</strong></p>';
    echo '<div id="custom-colors-wrapper">';

    foreach ( $colors as $i => $color ) {
        ?>
        <div class="custom-color-row">
            <input type="text"
                   name="_custom_colors[<?php echo $i; ?>][name]"
                   placeholder="Color name"
                   value="<?php echo esc_attr( $color['name'] ); ?>" />

            <input type="color"
                   class="color-picker"
                   value="<?php echo esc_attr( $color['hex'] ); ?>" />

            <input type="text"
                   class="hex-input"
                   name="_custom_colors[<?php echo $i; ?>][hex]"
                   placeholder="#000000"
                   value="<?php echo esc_attr( $color['hex'] ); ?>" />

            <input type="text"
                   class="rgb-input"
                   placeholder="255,0,0" />

            <button type="button" class="button remove-color">×</button>
        </div>
        <?php
    }

    echo '</div>';
    echo '<button type="button" class="button" id="add-custom-color">Add Color</button>';
    echo '</div>';
});

/* =========================================
 * SAVE COLORS (HEX ONLY)
 * ========================================= */
add_action( 'woocommerce_admin_process_product_object', function ( $product ) {

    if ( empty( $_POST['_custom_colors'] ) || ! is_array( $_POST['_custom_colors'] ) ) {
        $product->update_meta_data( '_custom_colors', [] );
        return;
    }

    $clean = [];

    foreach ( $_POST['_custom_colors'] as $color ) {

        if ( empty( $color['name'] ) || empty( $color['hex'] ) ) {
            continue;
        }

        $hex = sanitize_hex_color( $color['hex'] );
        if ( ! $hex ) {
            continue;
        }

        $clean[] = [
            'name' => sanitize_text_field( $color['name'] ),
            'hex'  => $hex,
        ];
    }

    $product->update_meta_data( '_custom_colors', $clean );
});

/* =========================================
 * ADMIN ASSETS
 * ========================================= */
add_action( 'admin_enqueue_scripts', function ( $hook ) {

    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'product' ) {
        return;
    }

    wp_enqueue_script(
        'product-colors-admin-js',
        plugin_dir_url( __FILE__ ) . 'assets/product-colors-admin.js',
        [ 'jquery' ],
        '1.1.0',
        true
    );

    wp_enqueue_style(
        'product-colors-admin-css',
        plugin_dir_url( __FILE__ ) . 'assets/product-colors-admin.css',
        [],
        '1.1.0'
    );
});
