<?php
/**
 * Server-side render: just echo the nested content.
 * Your PDF generator can loop over the <div class="wizzy-page"> wrappers
 * and treat each as one page.
 */
function datasheets_multipage_render( $attributes, $content ) {
	return '<div class="datasheet-multipage">' . $content . '</div>';
}
register_block_type(
	__DIR__,
	[ 'render_callback' => 'datasheets_multipage_render' ]
);
