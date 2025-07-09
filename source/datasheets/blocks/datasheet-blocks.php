<?php
/**
 * Registers the “Datasheet” block and its dynamic render callback.
 */
add_action( 'init', function () {

    // Path to the *compiled* folder, not src/.
    $build_dir = plugin_dir_path( __FILE__ ) . 'build/blocks/datasheet';

    if ( ! file_exists( $build_dir . '/block.json' ) ) {
        // Developer forgot to run npm build; skip registration.
        return;
    }

    register_block_type(
        $build_dir,
        [
            'render_callback' => 'wizzy_render_datasheet_block',
        ]
    );
} );

function wizzy_render_datasheet_block( $atts, $content ) {
    if ( empty( $atts['layoutId'] ) ) {
        return '';
    }

    $meta = get_post_meta( (int) $atts['layoutId'], '_ds_layout_settings', true );
    if ( empty( $meta ) ) {
        return '';
    }

    $unit   = $meta['margin_unit'] ?? 'mm';
    $width  = $meta['width']  . $meta['width_unit'];
    $height = $meta['height'] . $meta['height_unit'];

    if ( ! empty( $meta['margin_equal'] ) ) {
        $m = $meta['margin'] . $unit;
        $padding = $m;
    } else {
        $padding = sprintf(
            '%s %s %s %s',
            $meta['margin_top']    . $unit,
            $meta['margin_right']  . $unit,
            $meta['margin_bottom'] . $unit,
            $meta['margin_left']   . $unit
        );
    }

    $style = sprintf(
        'width:%s;height:%s;padding:%s;box-sizing:border-box;',
        $width,
        $height,
        $padding
    );

    return sprintf(
        '<div class="datasheet-layout" style="%s">%s</div>',
        esc_attr( $style ),
        $content
    );
}
