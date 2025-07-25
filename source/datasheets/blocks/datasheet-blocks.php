<?php
/**
 * Registers the “Datasheet” block and its dynamic render callback.
 */
 
 
/**
 * ------------------------------------------------------------------
 * 1.  Expose layout meta to the REST API
 * ------------------------------------------------------------------
 */
add_action( 'init', 'ds_register_layout_meta' );
function ds_register_layout_meta() {
    register_meta(
        'post',
        '_ds_layout_settings',
        [
            'single'       => true,
            'type'         => 'object',
            'show_in_rest' => true,
            'auth_callback'=> '__return_true',   // or your custom caps check
        ]
    );
}


/**
 * ------------------------------------------------------------------
 * 2.  Register the Datasheet block
 * ------------------------------------------------------------------
 */
add_action( 'init', function () {

    // Path to compiled assets (plugin_root/build/blocks/datasheet)
    $build_dir = dirname( __DIR__ ) . '/build/blocks/datasheet';

    if ( ! file_exists( $build_dir . '/block.json' ) ) {
        // Build hasn’t been run: skip block registration,
        // but meta is still registered by the function above.
        return;
    }

    register_block_type(
        $build_dir,
        [
            'render_callback' => 'render_datasheet_block',
        ]
    );
} );



/**
 * Enqueue datasheet front-end CSS if a theme bypasses `the_content`.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_admin() ) {
			// return;                       // editor === handled by `editorStyle`
		}
		
		error_log("Testing the css script");

		// Core already loaded it? Nothing to do.
		if ( wp_style_is( 'datasheets-datasheet-view-style', 'enqueued' ) ) {
			return;
		}

		/*
		 * Detect the block in *any* post in the main query.
		 * `has_block()` parses raw post_content, so it works even when
		 * the_theme() echoes `get_the_content()` without filters.
		 */
		global $wp_query;
		foreach ( (array) $wp_query->posts as $post ) {
			if ( has_block( 'datasheets/datasheet', $post ) ) {
				wp_enqueue_style( 'datasheets-datasheet-view-style' );
				break;
			}
		}
	},
	20          // after most themes have registered their assets
);



function render_datasheet_block( $atts, $content ) {
    if ( empty( $atts['layoutId'] ) ) {
        return '';
    }

    $meta = get_post_meta( (int) $atts['layoutId'], '_ds_layout_settings', true );
    if ( empty( $meta ) ) {
        return '';
    }
	
	if ( empty( $atts['layoutId'] ) || empty( $meta ) ) {
		return '<div class="datasheet-layout missing" style="border:1px dashed red;padding:1rem">' .
			esc_html__( 'Datasheet block: no layout selected or layout has no settings.', 'datasheets' ) .
			'</div>';
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
        'width:%s;height:%s;padding:%s;box-sizing:content-box;',
        $width,
        $height,
        $padding
    );

    return sprintf(
        '<div class="datasheet-layout" style="%s"><div class="datasheet-content">%s</div></div>',
		esc_attr( $style ),
		$content         // inner blocks go inside the inner wrapper
    );
}
