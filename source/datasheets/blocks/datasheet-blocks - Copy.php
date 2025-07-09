<?php

// datasheets-blocks.php
add_action( 'init', function () {
    // JS bundle from @wordpress/scripts (or any bundler)
    wp_register_script(
        'wizzy-datasheet-block',
        plugins_url( 'blocks/datasheet/edit.js', __FILE__ ),
        [ 'wp-blocks', 'wp-element', 'wp-data', 'wp-components', 'wp-editor', 'wp-i18n' ],
        '1.0',
        true
    );

    register_block_type(
        'wizzy/datasheet',
        [
            'editor_script'   => 'wizzy-datasheet-block',
            'render_callback' => 'wizzy_render_datasheet_block',
            'attributes'      => [
                'layoutId'    => [ 'type' => 'integer', 'default' => 0 ],
                'styleString' => [ 'type' => 'string',  'default' => '' ],
            ],
        ]
    );
} );

function wizzy_render_datasheet_block( $atts, $content ) {
    if ( empty( $atts['layoutId'] ) ) {
        return ''; // nothing selected
    }

    $meta = get_post_meta( (int) $atts['layoutId'], '_ds_layout_settings', true );
    if ( empty( $meta ) ) {
        return '';
    }

    // Build inline style (front-end) exactly as JS did in editor.
    $unit   = isset( $meta['margin_unit'] ) ? $meta['margin_unit'] : 'mm';
    $width  = $meta['width']  . $meta['width_unit'];
    $height = $meta['height'] . $meta['height_unit'];

    if ( ! empty( $meta['margin_equal'] ) ) {
        $m = $meta['margin'] . $unit;
        $style = "width:$width;height:$height;padding:$m;box-sizing:border-box;";
    } else {
        $style = sprintf(
            'width:%s;height:%s;padding:%s %s %s %s;box-sizing:border-box;',
            $width,
            $height,
            $meta['margin_top']    . $unit,
            $meta['margin_right']  . $unit,
            $meta['margin_bottom'] . $unit,
            $meta['margin_left']   . $unit
        );
    }

    return sprintf(
        '<div class="datasheet-layout" style="%s">%s</div>',
        esc_attr( $style ),
        $content                // inner blocks
    );
}
